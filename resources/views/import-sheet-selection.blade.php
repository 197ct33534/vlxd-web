@extends('layouts.dashboard')

@section('title', 'Select Sheet to Import')

@section('content')
<div class="max-w-lg mx-auto bg-white shadow rounded-lg p-8 relative">
    <h2 class="text-2xl font-bold mb-6 text-center text-blue-700">📂 Select Sheet to Import</h2>

    <p class="mb-4 text-gray-600">The uploaded file contains multiple sheets. Please select the sheet you want to import data from:</p>

    <form action="{{ route('invoice.import') }}" method="POST" class="space-y-4">
        @csrf
        <input type="hidden" name="stored_file_path" value="{{ $storedFilePath }}">
        @if(isset($projectId))
            <input type="hidden" name="project_id" value="{{ $projectId }}">
        @endif

        <div class="space-y-2">
            @foreach($sheetNames as $index => $name)
                <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                    <input type="radio" name="sheet_index" value="{{ $index }}" class="form-radio h-5 w-5 text-blue-600" {{ $index === 0 ? 'checked' : '' }}>
                    <span class="text-gray-800 font-medium">{{ $name }}</span>
                </label>
            @endforeach
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-md transition mt-6">
            Continue Import ➡️
        </button>
    </form>
</div>
@endsection
