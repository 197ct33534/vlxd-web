@props(['name', 'label' => 'Image', 'value' => null, 'modalId' => 'imageManagerModal'])

<div x-data="{
    imageUrl: '{{ $value ?? '' }}',
    id: $id('image-picker'), // Generate unique ID for this instance
    
    openPicker() {
        $dispatch('open-image-manager', {
            targetId: '{{ $modalId }}',
            emitterId: this.id
        });
    },

    removeImage() {
        this.imageUrl = '';
    }
}"
x-on:image-selected.window="if ($event.detail.emitterId === id) { imageUrl = $event.detail.url; }"
class="space-y-2">

    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">{{ $label }}</label>
    
    <!-- Hidden Input for Form Submission -->
    <input type="hidden" name="{{ $name }}" :value="imageUrl">

    <!-- Preview Area -->
    <div class="relative bg-gray-50 dark:bg-slate-900 border-2 border-dashed border-gray-300 dark:border-slate-700 rounded-lg p-4 flex flex-col items-center justify-center min-h-[160px] text-center hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors"
         :class="{ 'border-solid border-primary bg-primary/5': imageUrl }">
        
        <!-- No Image State -->
        <div x-show="!imageUrl" class="space-y-2">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-slate-800 text-gray-500">
                <span class="material-symbols-outlined">image</span>
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                <button type="button" @click="openPicker()" class="font-semibold text-primary hover:text-indigo-500">Click to select</button> an image
            </div>
            <p class="text-xs text-gray-500">or paste URL below</p>
        </div>

        <!-- Selected Image State -->
        <div x-show="imageUrl" class="relative group w-full h-full flex items-center justify-center" style="display: none;">
            <img :src="imageUrl" class="max-h-[200px] rounded-lg shadow-sm object-contain">
            
            <div class="absolute inset-0 flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity bg-black/40 rounded-lg backdrop-blur-sm">
                <button type="button" @click="openPicker()" class="p-2 bg-white text-gray-700 rounded-full hover:bg-gray-100 shadow-sm" title="Change Image">
                    <span class="material-symbols-outlined text-[20px]">edit</span>
                </button>
                <button type="button" @click="removeImage()" class="p-2 bg-red-500 text-white rounded-full hover:bg-red-600 shadow-sm" title="Remove Image">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Direct URL Input (Optional Backup) -->
    <div class="relative mt-2">
        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
             <span class="material-symbols-outlined text-gray-400 text-[18px]">link</span>
        </span>
        <input type="text" x-model="imageUrl" class="block w-full rounded-md border-gray-300 pl-10 focus:border-primary focus:ring-primary sm:text-sm dark:bg-slate-900 dark:border-slate-700 dark:text-white" placeholder="https://...">
    </div>
</div>
