@extends('layouts.dashboard')

@section('title', 'Daily Delivery Reports')

@section('content')
<div class="flex flex-col gap-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-text-light dark:text-text-dark text-3xl font-black tracking-tight">Daily Reports</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Track and approve daily delivery activities.</p>
        </div>
        <a href="{{ route('daily-reports.create') }}" class="flex items-center gap-2 bg-primary hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors text-sm font-semibold shadow-lg shadow-blue-500/30">
            <span class="material-symbols-outlined text-lg">add_task</span>
            Create Report
        </a>
    </div>

    <!-- Reports List -->
    <div class="bg-container-light dark:bg-container-dark rounded-xl shadow-subtle overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-background-light dark:bg-background-dark">
                    <tr>
                        <th class="px-6 py-4 font-bold">Date</th>
                        <th class="px-6 py-4 font-bold">Reporter</th>
                        <th class="px-6 py-4 font-bold">Items</th>
                        <th class="px-6 py-4 font-bold text-center">Status</th>
                        <th class="px-6 py-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($reports as $report)
                        <tr class="hover:bg-primary/5 dark:hover:bg-primary/10 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 dark:text-white">
                                {{ $report->report_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary text-xs font-bold">
                                        {{ substr($report->user->name, 0, 2) }}
                                    </div>
                                    <span class="font-medium">{{ $report->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300 text-xs font-medium px-2.5 py-0.5 rounded border border-gray-200 dark:border-gray-700">
                                    {{ $report->items->count() }} items
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($report->status === 'approved')
                                    <span class="inline-flex items-center gap-1 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                        <span class="material-symbols-outlined text-sm">check_circle</span> Approved
                                    </span>
                                @elseif($report->status === 'rejected')
                                    <span class="inline-flex items-center gap-1 bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                        <span class="material-symbols-outlined text-sm">cancel</span> Rejected
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-yellow-900 dark:text-yellow-300">
                                        <span class="material-symbols-outlined text-sm">pending</span> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('daily-reports.show', $report->id) }}" class="text-blue-600 hover:text-blue-900 dark:hover:text-blue-400 font-medium hover:underline">View Details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-4xl text-gray-300">assignment_late</span>
                                    <p>No reports found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
            {{ $reports->links() }}
        </div>
    </div>
</div>
@endsection
