@extends('layouts.dashboard')

@section('title', 'Tất cả dự án')

@section('content')
    <div class="flex flex-col gap-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-text-light dark:text-text-dark text-3xl font-black tracking-tight">Tất cả dự án</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Truy cập hóa đơn và nhập Excel theo từng dự án. Để thêm dự án, vào <a href="{{ route('customers.index') }}" class="text-primary font-semibold hover:underline">Khách hàng</a> → chọn khách → Dự án.
                </p>
            </div>
        </div>

        <div class="bg-container-light dark:bg-container-dark rounded-xl shadow-subtle overflow-hidden border border-border-light dark:border-border-dark">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-background-light dark:bg-background-dark">
                        <tr>
                            <th class="px-6 py-4 font-bold">Khách hàng</th>
                            <th class="px-6 py-4 font-bold">Dự án</th>
                            <th class="px-6 py-4 font-bold text-center">Tổng HĐ</th>
                            <th class="px-6 py-4 font-bold text-center">Đã thu</th>
                            <th class="px-6 py-4 font-bold text-center">Công nợ</th>
                            <th class="px-6 py-4 font-bold text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                            <tr class="bg-container-light dark:bg-container-dark border-b dark:border-border-dark hover:bg-primary/5 dark:hover:bg-primary/10 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    <a href="{{ route('customers.projects.index', $project->customer_id) }}" class="hover:text-primary hover:underline">
                                        {{ $project->customer->name ?? '—' }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-gray-900 dark:text-white">{{ $project->name }}</td>
                                <td class="px-6 py-4 text-center">{{ number_format($project->total_invoice ?? 0) }}</td>
                                <td class="px-6 py-4 text-center text-green-600">{{ number_format($project->total_paid ?? 0) }}</td>
                                <td class="px-6 py-4 text-center text-red-600 font-semibold">{{ number_format($project->total_debt ?? 0) }}</td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('projects.invoices.index', $project->id) }}"
                                       class="inline-flex p-1.5 text-gray-500 dark:text-gray-400 hover:text-blue-600 rounded-full transition-colors"
                                       title="Hóa đơn">
                                        <span class="material-symbols-outlined text-xl">receipt_long</span>
                                    </a>
                                    <a href="{{ route('invoice.index', ['project_id' => $project->id]) }}"
                                       class="inline-flex p-1.5 text-gray-500 dark:text-gray-400 hover:text-green-600 rounded-full transition-colors"
                                       title="Nhập hóa đơn Excel">
                                        <span class="material-symbols-outlined text-xl">upload_file</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">Chưa có dự án nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-border-light dark:border-border-dark">
                {{ $projects->links() }}
            </div>
        </div>
    </div>
@endsection
