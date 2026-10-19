@extends('layouts.dashboard')

@section('title', 'Phiếu nhập kho')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between gap-3">
        <h1 class="mf-admin-title text-3xl font-black">Phiếu nhập kho</h1>
        <a href="{{ route('admin.stock-receipts.create') }}" class="rounded-lg bg-primary text-white font-bold px-4 py-3">Tạo phiếu</a>
    </div>
    <div class="bg-white rounded-xl shadow-subtle overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-2">Mã</th>
                    <th class="px-4 py-2">Ngày</th>
                    <th class="px-4 py-2">Nhà cung cấp</th>
                    <th class="px-4 py-2 text-right">Tiền hàng</th>
                </tr>
            </thead>
            <tbody>
                @forelse($receipts as $receipt)
                    <tr class="border-t">
                        <td class="px-4 py-2 font-bold">{{ $receipt->code }}</td>
                        <td class="px-4 py-2">{{ $receipt->receipt_date?->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">{{ $receipt->supplier?->name }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($receipt->total_amount, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-gray-500">Chưa có phiếu nhập.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $receipts->links() }}
</div>
@endsection
