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
            <form method="POST" action="{{ route('admin.customers.credit', $row['customer']) }}" class="grid grid-cols-2 gap-2 items-end text-sm md:flex md:flex-wrap">
                @csrf
                <label class="min-w-0">Hạn mức khách <span class="text-gray-400">(0 = không giới hạn)</span>
                    <input type="number" name="credit_limit" min="0" step="1000" value="{{ $row['customer']->credit_limit }}" class="block w-full rounded-lg border-gray-300 md:w-auto">
                </label>
                <label class="min-w-0">Số ngày nợ
                    <input type="number" name="credit_days" min="0" value="{{ $row['customer']->credit_days }}" class="block w-full rounded-lg border-gray-300 md:w-24">
                </label>
                <button class="col-span-2 min-h-[44px] rounded-lg bg-gray-800 text-white font-bold px-3 py-2 md:min-h-0">Lưu hạn mức</button>
            </form>

            {{-- Mobile: mỗi công trình một khối xếp dọc (bảng 5 cột kèm 2 form phải cuộn ngang). --}}
            <div data-mobile-debt-projects class="flex flex-col gap-3 md:hidden">
                @foreach($row['projects'] as $line)
                    <div class="rounded-lg border border-gray-200 p-3 space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-semibold leading-snug">{{ $line['project']->name }}</p>
                                <p class="mt-0.5 text-xs {{ $line['credit']['is_exceeded'] ? 'font-semibold text-red-700' : 'text-emerald-700' }}">
                                    {{ $line['credit']['is_exceeded'] ? $line['credit']['reason'] : 'Bình thường' }}
                                </p>
                            </div>
                            <div class="shrink-0 text-right">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">Nợ</p>
                                <p class="font-bold text-red-600">{{ number_format($line['project']->total_debt, 0, ',', '.') }} đ</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.projects.credit', $line['project']) }}" class="grid grid-cols-2 gap-2 text-xs">
                            @csrf
                            <label class="min-w-0 text-gray-500">Hạn mức riêng
                                <input type="number" name="credit_limit" min="0" step="1000" placeholder="theo khách" value="{{ $line['project']->credit_limit }}" class="mt-0.5 block w-full rounded-lg border-gray-300 text-sm">
                            </label>
                            <label class="min-w-0 text-gray-500">Số ngày nợ
                                <input type="number" name="credit_days" min="0" placeholder="theo khách" value="{{ $line['project']->credit_days }}" class="mt-0.5 block w-full rounded-lg border-gray-300 text-sm">
                            </label>
                            <button class="col-span-2 min-h-[44px] rounded-lg bg-primary/10 text-sm font-bold text-primary active:bg-primary/20">Lưu hạn mức công trình</button>
                        </form>

                        <form method="GET" action="{{ route('admin.projects.reconciliation-pdf', $line['project']) }}" class="grid grid-cols-2 gap-2 text-xs" target="_blank">
                            <label class="min-w-0 text-gray-500">Đối soát từ
                                <input type="date" name="from_date" value="2000-01-01" class="mt-0.5 block w-full rounded-lg border-gray-300 text-sm">
                            </label>
                            <label class="min-w-0 text-gray-500">Đến
                                <input type="date" name="to_date" value="{{ now()->toDateString() }}" class="mt-0.5 block w-full rounded-lg border-gray-300 text-sm">
                            </label>
                            <button class="col-span-2 inline-flex min-h-[44px] items-center justify-center gap-1.5 rounded-lg border border-gray-300 text-sm font-bold text-gray-700 active:bg-gray-100">
                                <span class="material-symbols-outlined text-lg leading-none">picture_as_pdf</span>
                                Xuất PDF đối soát
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            <div class="hidden md:block overflow-x-auto" data-desktop-debt-table>
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
