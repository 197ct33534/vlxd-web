@extends('layouts.dashboard')

@section('title', 'Invoice Details #' . ($invoice->code ?? $invoice->id))

@section('content')
    <div class="flex flex-col gap-6 max-w-5xl mx-auto">
        <!-- Header / Back Navigation -->
        <div class="flex items-center justify-between">
            <a href="{{ route('projects.invoices.index', $invoice->project_id) }}" class="flex items-center gap-2 text-gray-500 hover:text-primary transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
                <span class="font-medium">Back to Invoices</span>
            </a>
            <div class="flex gap-2">
                <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    <span class="material-symbols-outlined text-sm">print</span>
                    <span>Print</span>
                </button>
            </div>
        </div>

        <!-- Invoice Card -->
        @include('invoices.partials.details')
    </div>
@endsection
