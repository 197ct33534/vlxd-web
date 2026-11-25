@extends('layouts.dashboard')

@section('title', 'Xem trước nhập Excel')
@section('page_heading', 'Nhập hóa đơn Excel')

@section('content')
<div class="mx-auto max-w-5xl space-y-6 px-4 py-5"
    x-data="{ comparedProject: @js((string) ($project?->id ?? '')), selectedProject: @js((string) ($project?->id ?? '')) }">
    <div>
        <h1 class="text-2xl font-bold">Xem trước nhập Excel</h1>
        <p class="break-all text-sm text-gray-500">{{ $workbook['filename'] }} · {{ count($sheets) }} sheet</p>
    </div>

    @if ($errors->any())
        <ul role="alert" class="space-y-1 rounded-lg bg-red-50 p-4 text-sm text-red-800">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('invoice.workbook', $token) }}" method="GET" class="space-y-2 rounded-xl border p-4">
        <label for="workbook-project" class="block font-semibold">Công trình để đối chiếu và lưu</label>
        <div class="flex flex-wrap gap-2">
            <select id="workbook-project" name="project_id" x-model="selectedProject" class="min-w-0 flex-1 rounded-lg border-gray-300 text-sm dark:bg-slate-800">
                <option value="">Tạo công trình mới</option>
                @foreach ($projects as $option)
                    <option value="{{ $option->id }}" @selected($option->id === $project?->id)>
                        #{{ $option->id }} · {{ $option->customer->name }} · {{ $option->name }}@if($option->address) · {{ $option->address }}@endif
                    </option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg border px-4 py-2 text-sm font-semibold">Đối chiếu</button>
        </div>
        <p x-cloak x-show="selectedProject !== comparedProject" class="text-sm">Bấm Đối chiếu để kiểm tra các sheet theo công trình vừa chọn.</p>
    </form>

    <form action="{{ route('invoice.workbook.save', $token) }}" method="POST" class="space-y-4"
        @submit="if (selectedProject !== comparedProject) $event.preventDefault()">
        @csrf
        @if ($project)
            <input type="hidden" name="project_id" value="{{ $project->id }}">
            <p class="text-sm">Nơi lưu: <a class="font-semibold underline" href="{{ route('projects.invoices.index', $project) }}">{{ $project->customer->name }} · {{ $project->name }}</a></p>
        @else
            <div>
                <label for="workbook-new-project" class="block text-sm font-semibold">Tên công trình mới</label>
                <input id="workbook-new-project" name="project_name" type="text" value="{{ old('project_name') }}" required maxlength="255"
                    class="mt-1 w-full rounded-lg border-gray-300 text-sm dark:bg-slate-800">
            </div>
        @endif

        @php
            $results = session('sheet_results', []);
            $labels = [
                'ready' => 'Sẵn sàng nhập', 'duplicate' => 'Đã nhập',
                'legacy' => 'Có thể đã nhập', 'changed' => 'Nội dung đã thay đổi',
                'overlap' => 'Có dòng giống hóa đơn cũ', 'other_project' => 'Đã nhập ở công trình khác',
                'empty' => 'Không có dòng vật liệu hoặc ứng tiền', 'invalid' => 'Ngày không hợp lệ',
                'same_sheet' => 'Trùng nội dung trong tệp',
            ];
        @endphp
        @foreach ($sheets as $index => $sheet)
            @php
                $match = $sheet['match'];
                $status = $match['status'];
                $needsReview = in_array($status, ['legacy', 'changed', 'overlap', 'other_project'], true);
                $result = $results[$index] ?? null;
            @endphp
            <section class="space-y-3 rounded-xl border p-4">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div class="min-w-0">
                        <h2 class="break-all font-semibold">{{ $sheet['name'] }}</h2>
                        <p class="text-sm text-gray-500">{{ $sheet['khachHang'] ?: 'Thiếu tên khách hàng' }} · {{ $sheet['diaChi'] ?: 'Thiếu địa chỉ công trình' }}</p>
                        <p class="text-sm">{{ count($sheet['items']) }} dòng · {{ number_format($sheet['tongTien'], 0, ',', '.') }} ₫ · Ngày giao cuối {{ $sheet['ngayCuoi'] ?: '—' }}</p>
                        @if ($sheet['advancePayments'])
                            <p class="text-sm">Ứng tiền {{ number_format($sheet['tongTienUng'], 0, ',', '.') }} ₫</p>
                        @endif
                    </div>
                    <div class="space-y-1 text-sm">
                        <p class="font-semibold">{{ $labels[$status] }}</p>
                        @if ($status === 'ready' || $needsReview)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="sheets[]" value="{{ $index }}"
                                    @checked(in_array($index, (array) old('sheets', $status === 'ready' ? [$index] : [])))>
                                Chọn nhập
                            </label>
                        @endif
                    </div>
                </div>

                @if ($status === 'ready' || $needsReview)
                    <div>
                        <label for="invoice-date-{{ $index }}" class="block text-sm font-semibold">Ngày xuất hóa đơn</label>
                        <input id="invoice-date-{{ $index }}" type="date" name="invoice_dates[{{ $index }}]"
                            value="{{ old('invoice_dates.'.$index, $sheet['invoiceDate'] ?? '') }}"
                            class="mt-1 rounded-lg border-gray-300 text-sm dark:bg-slate-800">
                        @empty($sheet['invoiceDate'])
                            <p class="mt-1 text-sm text-gray-500">Hãy điền ngày xuất nếu chọn nhập sheet này.</p>
                        @endempty
                    </div>
                @elseif (!empty($sheet['invoiceDate']))
                    <p class="text-sm">Ngày xuất hóa đơn trong Excel {{ \Carbon\Carbon::parse($sheet['invoiceDate'])->format('d/m/Y') }}</p>
                @endif

                @if (isset($match['invoice']))
                    <div class="space-y-1 text-sm">
                        @foreach ($match['matches'] ?? [$match['invoice']] as $invoice)
                            <a href="{{ route('invoices.show', $invoice) }}" class="underline" target="_blank" rel="noopener">Hóa đơn #{{ $invoice->id }}</a>
                            <span>{{ number_format($invoice->total_amount, 0, ',', '.') }} ₫</span>
                        @endforeach
                        @if ($status === 'other_project')
                            <p>Công trình {{ $match['invoice']->project->name }}</p>
                        @endif
                        @if (isset($match['shared']))
                            <p>{{ $match['shared'] }}/{{ count($sheet['items']) }} dòng vật liệu khớp. Chênh lệch {{ number_format($sheet['tongTien'] - $match['invoice']->total_amount, 0, ',', '.') }} ₫.</p>
                        @endif
                        @if ($status === 'legacy')
                            <p>Khớp dòng vật liệu và tổng tiền. Hãy đối chiếu ngày giao hàng trước khi quyết định.</p>
                        @endif
                        @if ($needsReview)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="reviewed[]" value="{{ $index }}" @checked(in_array($index, (array) old('reviewed', [])))>
                                Đã đối chiếu và muốn tạo hóa đơn mới cho sheet này
                            </label>
                        @endif
                    </div>
                @elseif ($status === 'same_sheet')
                    <p class="text-sm">Nội dung giống sheet {{ $match['sheet'] }}.</p>
                @elseif ($status === 'invalid')
                    <p class="text-sm text-red-700">{{ $match['error'] }} Hãy sửa trong Excel rồi tải lại.</p>
                @endif

                @if ($result)
                    @if ($result['status'] === 'error')
                        <p role="alert" class="text-sm text-red-700">{{ $result['error'] }}</p>
                    @else
                        <p class="text-sm font-semibold">{{ $result['status'] === 'saved' ? 'Vừa lưu' : 'Đã bỏ qua bản trùng' }} · Hóa đơn #{{ $result['invoice_id'] }}</p>
                    @endif
                @endif

                @if ($sheet['items'] || $sheet['advancePayments'])
                    <details class="text-sm">
                        <summary class="cursor-pointer font-semibold">Xem ngày và các dòng trong sheet</summary>
                        <ul class="mt-2 divide-y">
                            @foreach ($sheet['items'] as $item)
                                <li class="py-2">{{ $item['ngay_thang'] ?: 'Không có ngày' }} · {{ $item['ten_vat_lieu'] }} · {{ $item['so_luong'] }} {{ $item['don_vi_tinh'] }} × {{ number_format($item['don_gia'], 0, ',', '.') }} ₫</li>
                            @endforeach
                            @foreach ($sheet['advancePayments'] as $payment)
                                <li class="py-2">{{ $payment['ngay_thang'] ?: 'Dùng ngày hóa đơn' }} · {{ $payment['noi_dung'] }} · {{ number_format($payment['so_tien'], 0, ',', '.') }} ₫</li>
                            @endforeach
                        </ul>
                    </details>
                @endif
            </section>
        @endforeach

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="confirmed" value="1" required>
            Tôi đã kiểm tra công trình và ngày của các sheet đã chọn
        </label>
        <button type="submit" :disabled="selectedProject !== comparedProject"
            class="rounded-lg bg-primary px-5 py-3 font-semibold text-white disabled:opacity-50">Lưu các sheet đã chọn</button>
    </form>
</div>
@endsection
