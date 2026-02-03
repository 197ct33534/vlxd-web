@extends('layouts.dashboard')

@section('title', 'Report Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('daily-reports.index') }}" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <h1 class="text-text-light dark:text-text-dark text-2xl font-bold tracking-tight">Report Details</h1>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 ml-9">
                Submitted by <span class="font-semibold text-primary">{{ $dailyReport->user->name }}</span> for {{ $dailyReport->report_date->format('d/m/Y') }}
            </p>
        </div>
        
        <!-- Status Badge -->
        @if($dailyReport->status === 'approved')
            <span class="bg-green-100 text-green-800 text-sm font-bold px-3 py-1 rounded-full dark:bg-green-900 dark:text-green-300 flex items-center gap-1">
                <span class="material-symbols-outlined text-base">check_circle</span> Approved
            </span>
        @elseif($dailyReport->status === 'rejected')
            <span class="bg-red-100 text-red-800 text-sm font-bold px-3 py-1 rounded-full dark:bg-red-900 dark:text-red-300 flex items-center gap-1">
                <span class="material-symbols-outlined text-base">cancel</span> Rejected
            </span>
        @else
            <span class="bg-yellow-100 text-yellow-800 text-sm font-bold px-3 py-1 rounded-full dark:bg-yellow-900 dark:text-yellow-300 flex items-center gap-1">
                <span class="material-symbols-outlined text-base">pending</span> Pending Approval
            </span>
        @endif
    </div>

    <!-- Rejection Note (if any) -->
    @if($dailyReport->admin_note)
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4">
            <h4 class="text-sm font-bold text-red-800 dark:text-red-300 mb-1">Admin Note:</h4>
            <p class="text-sm text-red-700 dark:text-red-400">{{ $dailyReport->admin_note }}</p>
        </div>
    @endif

    <!-- Items Table -->
    <div class="bg-container-light dark:bg-container-dark rounded-xl shadow-subtle overflow-hidden mb-6">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-6 py-4 font-bold">Project</th>
                    <th class="px-6 py-4 font-bold">Product</th>
                    <th class="px-6 py-4 font-bold">Unit</th>
                    <th class="px-6 py-4 font-bold text-right">Quantity</th>
                    <th class="px-6 py-4 font-bold text-right">Price</th>
                    <th class="px-6 py-4 font-bold text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($dailyReport->items as $item)
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $item->project->name }}</td>
                        <td class="px-6 py-4">{{ $item->product_name }}</td>
                        <td class="px-6 py-4">{{ $item->unit }}</td>
                        <td class="px-6 py-4 text-right">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right">{{ number_format($item->price, 0, ',', '.') }} đ</td>
                        <td class="px-6 py-4 text-right font-bold">{{ number_format($item->quantity * $item->price, 0, ',', '.') }} đ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Admin Actions -->
    @if(Auth::user()->role === 'admin' && $dailyReport->status === 'pending')
        <div class="bg-gray-50 dark:bg-gray-800/50 p-6 rounded-xl border border-gray-100 dark:border-gray-700">
            <h3 class="font-bold text-gray-900 dark:text-white mb-4">Admin Actions</h3>
            <div class="flex flex-col sm:flex-row gap-4">
                <form action="{{ route('daily-reports.approve', $dailyReport->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold flex items-center gap-2 shadow-sm transition-colors">
                        <span class="material-symbols-outlined">check</span> Approve Report
                    </button>
                </form>

                <div x-data="{ showReason: false }" class="flex-1">
                    <button @click="showReason = !showReason" type="button" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold flex items-center gap-2 shadow-sm transition-colors">
                        <span class="material-symbols-outlined">block</span> Reject Report
                    </button>
                    
                    <form x-show="showReason" action="{{ route('daily-reports.reject', $dailyReport->id) }}" method="POST" class="mt-4">
                        @csrf @method('PUT')
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reason for rejection:</label>
                        <textarea name="admin_note" rows="2" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white mb-2"></textarea>
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-1.5 rounded text-sm font-medium">Confirm Reject</button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
