@props(['projects', 'projectId' => null, 'formId' => null])

<fieldset class="mb-4 space-y-2 text-sm" x-data="{ destination: '' }">
    <legend class="font-semibold">Công trình nhận hóa đơn</legend>
    @if ($projectId && ($origin = $projects->firstWhere('id', $projectId)))
        <p>Bạn mở trang nhập từ công trình #{{ $origin->id }} · {{ $origin->name }}.</p>
    @endif
    <label for="import-project-id" class="block">Chọn công trình có sẵn</label>
    <select id="import-project-id" name="project_id" x-model="destination"
        @if ($formId) form="{{ $formId }}" @endif
        class="w-full rounded-lg border-gray-300 text-sm dark:bg-slate-800">
        <option value="">Tạo công trình mới</option>
        @foreach ($projects as $project)
            <option value="{{ $project->id }}">#{{ $project->id }} · {{ $project->name }} · {{ $project->customer->name }}@if($project->address) · {{ $project->address }}@endif</option>
        @endforeach
    </select>
    <div x-show="!destination">
        <label for="import-project-name" class="block">Tên công trình mới</label>
        <input id="import-project-name" name="project_name" type="text" maxlength="255"
            @if ($formId) form="{{ $formId }}" @endif
            :required="!destination" :disabled="!!destination"
            class="w-full rounded-lg border-gray-300 text-sm dark:bg-slate-800">
    </div>
</fieldset>
