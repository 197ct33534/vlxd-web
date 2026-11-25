@props(['invoice', 'projects'])

<div x-data="{ destination: '' }" class="print:hidden">
    <button type="button" aria-haspopup="dialog" @click="$refs.moveProject.showModal()"
        class="inline-flex min-h-[44px] items-center gap-2 rounded-lg border border-primary px-4 text-sm font-bold text-primary hover:bg-primary/10">
        <span aria-hidden="true" class="material-symbols-outlined text-lg">drive_file_move</span>
        Chuyển công trình
    </button>
    <dialog x-ref="moveProject" aria-labelledby="move-invoice-heading"
        class="fixed inset-0 m-auto w-[calc(100%_-_2rem)] max-w-md rounded-2xl bg-container-light p-6 text-text-light shadow-2xl backdrop:bg-slate-950/75 dark:bg-container-dark dark:text-text-dark">
        <h2 id="move-invoice-heading" class="text-xl font-bold">Chuyển hóa đơn {{ $invoice->code ?: '#'.$invoice->id }}</h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $invoice->project->customer->name }} · Từ {{ $invoice->project->name }}</p>
        <form method="POST" action="{{ route('invoices.move-project', $invoice) }}" class="mt-4 space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label for="move-project-id" class="block text-sm font-semibold">Công trình đích</label>
                <select id="move-project-id" name="project_id" x-model="destination" class="mt-1 w-full rounded-lg border-border-light dark:border-border-dark dark:bg-slate-800">
                    <option value="">Tạo công trình mới</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            <div x-show="!destination">
                <label for="move-project-name" class="block text-sm font-semibold">Tên công trình mới</label>
                <input id="move-project-name" name="project_name" maxlength="255" :required="!destination" :disabled="!!destination"
                    class="mt-1 w-full rounded-lg border-border-light dark:border-border-dark dark:bg-slate-800">
            </div>
            <div class="flex flex-wrap justify-end gap-2">
                <button type="button" autofocus @click="$refs.moveProject.close()" class="min-h-[44px] rounded-lg border border-border-light px-4 font-semibold dark:border-border-dark">Hủy</button>
                <button type="submit" class="min-h-[44px] rounded-lg bg-primary px-4 font-bold text-white">Xác nhận chuyển</button>
            </div>
        </form>
    </dialog>
</div>
