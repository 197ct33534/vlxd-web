@extends('layouts.dashboard')

@section('title', 'Sổ nợ khách')

@section('content')
<div class="flex flex-col gap-6">
    <h1 class="mf-admin-title text-3xl font-black">Sổ nợ theo khách hàng</h1>
    @foreach($customers as $row)
        <section class="bg-white rounded-xl shadow-subtle p-4 space-y-3">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-black">{{ $row['customer']->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $row['customer']->phone }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Tổng công nợ</p>
                    <p class="text-xl font-black text-red-600">{{ number_format($row['debt'], 0, ',', '.') }} đ</p>
                    <span class="inline-block mt-1 text-xs font-bold rounded-full px-2 py-1 {{ $row['exceeded'] ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800' }}">
                        {{ $row['exceeded'] ? 'Vượt ngưỡng' : 'Bình thường' }}
                    </span>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.customers.credit', $row['customer']) }}" class="flex flex-wrap gap-2 items-end text-sm">
                @csrf
                <label>Hạn mức khách (0 = không giới hạn)
                    <input type="number" name="credit_limit" min="0" step="1000" value="{{ $row['customer']->credit_limit }}" class="block rounded-lg border-gray-300">
                </label>
                <label>Số ngày nợ
                    <input type="number" name="credit_days" min="0" value="{{ $row['customer']->credit_days }}" class="block w-24 rounded-lg border-gray-300">
                </label>
                <button class="rounded-lg bg-gray-800 text-white font-bold px-3 py-2">Lưu hạn mức</button>
            </form>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500">
                        <tr>
                            <th class="py-1">Công trình</th>
                            <th class="py-1 text-right">Nợ</th>
                            <th class="py-1">Hạn mức riêng</th>
                            <th class="py-1">Trạng thái</th>
                            <th class="py-1">Đối soát</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($row['projects'] as $line)
                            <tr class="border-t align-top">
                                <td class="py-2 font-semibold">{{ $line['project']->name }}</td>
                                <td class="py-2 text-right">{{ number_format($line['project']->total_debt, 0, ',', '.') }}</td>
                                <td class="py-2">
                                    <form method="POST" action="{{ route('admin.projects.credit', $line['project']) }}" class="flex flex-wrap gap-1">
                                        @csrf
                                        <input type="number" name="credit_limit" min="0" step="1000" placeholder="theo khách" value="{{ $line['project']->credit_limit }}" class="w-28 rounded border-gray-300 text-xs">
                                        <input type="number" name="credit_days" min="0" placeholder="ngày" value="{{ $line['project']->credit_days }}" class="w-16 rounded border-gray-300 text-xs">
                                        <button class="text-xs font-bold text-primary">Lưu</button>
                                    </form>
                                </td>
                                <td class="py-2">{{ $line['credit']['is_exceeded'] ? $line['credit']['reason'] : 'Bình thường' }}</td>
                                <td class="py-2">
                                    <form method="GET" action="{{ route('admin.projects.reconciliation-pdf', $line['project']) }}" class="flex flex-wrap gap-1" target="_blank">
                                        <input type="date" name="from_date" value="2000-01-01" class="rounded border-gray-300 text-xs">
                                        <input type="date" name="to_date" value="{{ now()->toDateString() }}" class="rounded border-gray-300 text-xs">
                                        <button class="text-xs font-bold text-primary">PDF</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endforeach
</div>
@endsection
