<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\Project;
use App\Services\ShopNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('portalCustomer');

        $orders = Order::query()
            ->where('customer_id', $customer->id)
            ->with('project')
            ->latest()
            ->paginate(20);

        return view('portal.orders.index', compact('orders', 'customer'));
    }

    public function create(Request $request)
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('portalCustomer');

        $projects = $customer->projects()->orderBy('name')->get();
        $selectedProjectId = $request->integer('project_id') ?: $projects->first()?->id;

        $suggestions = [];
        if ($selectedProjectId) {
            $suggestions = InvoiceItem::query()
                ->select('product_name', 'unit', DB::raw('MAX(price) as price'))
                ->whereIn('invoice_id', function ($q) use ($selectedProjectId) {
                    $q->select('id')->from('invoices')->where('project_id', $selectedProjectId);
                })
                ->groupBy('product_name', 'unit')
                ->orderBy('product_name')
                ->limit(20)
                ->get();
        }

        $reorder = null;
        if ($request->filled('reorder')) {
            $reorder = Order::with('items')
                ->where('customer_id', $customer->id)
                ->find($request->integer('reorder'));
        }

        return view('portal.orders.create', compact(
            'customer',
            'projects',
            'selectedProjectId',
            'suggestions',
            'reorder'
        ));
    }

    public function store(Request $request)
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('portalCustomer');

        $data = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'delivery_address' => 'nullable|string|max:500',
            'note' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.unit' => 'required|string|max:50',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'nullable|numeric|min:0',
        ]);

        if (!empty($data['project_id'])) {
            $ownsProject = Project::where('id', $data['project_id'])
                ->where('customer_id', $customer->id)
                ->exists();
            if (!$ownsProject) {
                return back()->withInput()->with('error', 'Công trình không hợp lệ.');
            }
        }

        $order = DB::transaction(function () use ($customer, $data) {
            $project = !empty($data['project_id'])
                ? Project::find($data['project_id'])
                : null;

            $order = Order::create([
                'code' => Order::generateCode(),
                'customer_id' => $customer->id,
                'project_id' => $project?->id,
                'delivery_address' => $data['delivery_address'] ?: ($project?->address),
                'status' => 'pending_confirm',
                'note' => $data['note'] ?? null,
                'total_amount' => 0,
            ]);

            $total = 0;
            foreach ($data['items'] as $item) {
                $price = (float) ($item['price'] ?? 0);
                $qty = (float) $item['quantity'];
                $amount = round($price * $qty, 2);
                $total += $amount;

                $order->items()->create([
                    'product_name' => $item['product_name'],
                    'unit' => $item['unit'],
                    'quantity' => $qty,
                    'price' => $price,
                    'amount' => $amount,
                ]);
            }

            $order->update(['total_amount' => $total]);

            return $order->load(['customer', 'project', 'items']);
        });

        app(ShopNotifier::class)->notifyNewOrder($order);

        return redirect()
            ->route('portal.orders.show', $order)
            ->with('success', 'Đã gửi yêu cầu đặt hàng. Cửa hàng sẽ xác nhận sớm.');
    }

    public function show(Request $request, Order $order)
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('portalCustomer');

        if ($order->customer_id !== $customer->id) {
            abort(404);
        }

        $order->load(['items', 'project', 'invoice']);

        return view('portal.orders.show', compact('order', 'customer'));
    }

    /** Poll trạng thái đơn (portal) — không reload full page. */
    public function status(Request $request, Order $order)
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('portalCustomer');

        if ($order->customer_id !== $customer->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json([
            'id' => $order->id,
            'code' => $order->code,
            'status' => $order->status,
            'status_label' => $order->statusLabel(),
            'invoice_id' => $order->invoice_id,
            'delivering_at' => $order->delivering_at,
            'delivered_at' => $order->delivered_at,
        ]);
    }

    public function latestPrice(Request $request)
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('portalCustomer');

        $projectId = $request->integer('project_id');
        $productName = trim((string) $request->query('product_name'));

        if ($productName === '') {
            return response()->json(['price' => null]);
        }

        if ($projectId) {
            $owns = Project::where('id', $projectId)->where('customer_id', $customer->id)->exists();
            if (!$owns) {
                return response()->json(['price' => null], 403);
            }
        }

        // 1) Giá gần nhất trên hóa đơn (cùng công trình ưu tiên)
        $invoiceQuery = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('projects', 'invoices.project_id', '=', 'projects.id')
            ->where('projects.customer_id', $customer->id)
            ->where('invoice_items.product_name', 'LIKE', '%' . $productName . '%')
            ->when($projectId, fn ($q) => $q->where('invoices.project_id', $projectId))
            ->orderByDesc('invoices.invoice_date')
            ->orderByDesc('invoice_items.id')
            ->select(
                'invoice_items.product_name',
                'invoice_items.price',
                'invoice_items.unit',
                'invoices.invoice_date as priced_at'
            );

        $fromInvoice = $invoiceQuery->first();

        // 2) Fallback: đơn mobile đã xác nhận / đã giao
        $fromOrder = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.customer_id', $customer->id)
            ->whereIn('orders.status', ['confirmed', 'delivering', 'delivered'])
            ->where('order_items.product_name', 'LIKE', '%' . $productName . '%')
            ->when($projectId, fn ($q) => $q->where('orders.project_id', $projectId))
            ->orderByDesc('orders.confirmed_at')
            ->orderByDesc('order_items.id')
            ->select(
                'order_items.product_name',
                'order_items.price',
                'order_items.unit',
                'orders.confirmed_at as priced_at'
            )
            ->first();

        $pick = $fromInvoice;
        $source = 'invoice';
        if ($fromInvoice && $fromOrder) {
            $invTime = strtotime((string) $fromInvoice->priced_at) ?: 0;
            $ordTime = strtotime((string) $fromOrder->priced_at) ?: 0;
            if ($ordTime > $invTime) {
                $pick = $fromOrder;
                $source = 'order';
            }
        } elseif (!$fromInvoice && $fromOrder) {
            $pick = $fromOrder;
            $source = 'order';
        }

        if (!$pick) {
            return response()->json([
                'price' => null,
                'unit' => null,
                'product_name' => null,
                'source' => null,
                'priced_at' => null,
            ]);
        }

        return response()->json([
            'price' => (float) $pick->price,
            'unit' => $pick->unit,
            'product_name' => $pick->product_name,
            'source' => $source,
            'priced_at' => $pick->priced_at,
        ]);
    }

    public function suggestProducts(Request $request)
    {
        /** @var Customer $customer */
        $customer = $request->attributes->get('portalCustomer');

        $projectId = $request->integer('project_id');
        $q = trim((string) $request->query('q', ''));

        if ($projectId) {
            $owns = Project::where('id', $projectId)->where('customer_id', $customer->id)->exists();
            if (!$owns) {
                return response()->json(['data' => []], 403);
            }
        }

        $invoiceItems = DB::table('invoice_items')
            ->join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->join('projects', 'invoices.project_id', '=', 'projects.id')
            ->where('projects.customer_id', $customer->id)
            ->when($projectId, fn ($query) => $query->where('invoices.project_id', $projectId))
            ->when($q !== '', fn ($query) => $query->where('invoice_items.product_name', 'LIKE', '%' . $q . '%'))
            ->orderByDesc('invoices.invoice_date')
            ->select('invoice_items.product_name', 'invoice_items.unit', 'invoice_items.price', 'invoices.invoice_date as priced_at')
            ->limit(40)
            ->get();

        $orderItems = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.customer_id', $customer->id)
            ->whereIn('orders.status', ['confirmed', 'delivering', 'delivered', 'pending_confirm'])
            ->when($projectId, fn ($query) => $query->where('orders.project_id', $projectId))
            ->when($q !== '', fn ($query) => $query->where('order_items.product_name', 'LIKE', '%' . $q . '%'))
            ->orderByDesc('orders.created_at')
            ->select('order_items.product_name', 'order_items.unit', 'order_items.price', 'orders.created_at as priced_at')
            ->limit(40)
            ->get();

        $merged = $invoiceItems->concat($orderItems)
            ->sortByDesc('priced_at')
            ->unique(fn ($row) => mb_strtolower($row->product_name . '|' . $row->unit))
            ->take(12)
            ->values()
            ->map(fn ($row) => [
                'product_name' => $row->product_name,
                'unit' => $row->unit,
                'price' => (float) $row->price,
                'priced_at' => $row->priced_at,
            ]);

        return response()->json(['data' => $merged]);
    }
}
