@extends('layouts.dashboard')

@section('title', 'New Daily Report')

@section('content')
<div class="max-w-4xl mx-auto pb-24" x-data="dailyReportForm()">
    <div class="flex items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-text-light dark:text-text-dark text-2xl font-bold tracking-tight mf-admin-title">Báo cáo ngày</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Nhập nhanh 1 tay tại hiện trường.</p>
        </div>
        <a href="{{ route('daily-reports.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center gap-1 text-sm font-medium mf-touch">
            <span class="material-symbols-outlined text-lg">arrow_back</span> Back
        </a>
    </div>

    <form action="{{ route('daily-reports.store') }}" method="POST" class="space-y-4">
        @csrf

        <div class="bg-container-light dark:bg-container-dark rounded-xl shadow-subtle p-4 md:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Người báo cáo</label>
                    <input type="text" value="{{ Auth::user()->name }}" disabled class="bg-gray-100 block w-full rounded-xl border-gray-300 text-sm mf-touch cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày</label>
                    <input type="date" name="report_date" value="{{ date('Y-m-d') }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring-primary text-base mf-touch">
                </div>
            </div>
        </div>

        <div class="bg-container-light dark:bg-container-dark rounded-xl shadow-subtle p-4 md:p-6">
            <div class="flex items-center justify-between mb-4 gap-2">
                <h3 class="text-lg font-medium text-gray-900">Dòng giao hàng</h3>
                <button type="button" @click="addItem()" class="text-primary text-sm font-bold flex items-center gap-1 mf-touch px-2">
                    <span class="material-symbols-outlined text-lg">add</span> Thêm
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="p-4 border border-gray-200 rounded-xl bg-gray-50 relative">
                        <button type="button" @click="removeItem(index)" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 mf-touch p-1" title="Xóa" x-show="items.length > 1">
                            <span class="material-symbols-outlined">close</span>
                        </button>

                        <div class="grid grid-cols-1 gap-3 md:grid-cols-12 md:gap-4 pr-8 md:pr-0">
                            <div class="md:col-span-3">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Công trình</label>
                                <select :name="'items[' + index + '][project_id]'" x-model="item.project_id" @change="fetchPrice(index)" required class="block w-full rounded-xl border-gray-300 text-base mf-touch">
                                    <option value="">Chọn CT</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Vật tư</label>
                                <input type="text" :name="'items[' + index + '][product_name]'" x-model="item.product_name" @blur="fetchPrice(index)" required placeholder="Xi măng, cát..." class="block w-full rounded-xl border-gray-300 text-base mf-touch">
                            </div>
                            <div class="grid grid-cols-3 gap-2 md:contents">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">ĐVT</label>
                                    <input type="text" :name="'items[' + index + '][unit]'" x-model="item.unit" required placeholder="bao/m3" class="block w-full rounded-xl border-gray-300 text-base mf-touch">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">SL</label>
                                    <input type="number" step="0.01" inputmode="decimal" :name="'items[' + index + '][quantity]'" x-model="item.quantity" required min="0" class="block w-full rounded-xl border-gray-300 text-base mf-touch">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">
                                        Giá
                                        <template x-if="item.isFetching"><span class="animate-pulse text-primary ml-1">...</span></template>
                                    </label>
                                    <input type="number" step="1" inputmode="numeric" :name="'items[' + index + '][price]'" x-model="item.price" required min="0" class="block w-full rounded-xl border-gray-300 text-base mf-touch mf-price">
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="mf-sticky-cta md:static md:bg-transparent md:border-0 md:p-0 md:mt-4 flex justify-stretch md:justify-end">
            <button type="submit" class="mf-cta-btn mf-cta-btn--primary md:w-auto md:px-8">
                Gửi báo cáo
            </button>
        </div>
    </form>
</div>

<script>
    function dailyReportForm() {
        return {
            items: [
                { project_id: '', product_name: '', unit: '', quantity: 1, price: 0, isFetching: false }
            ],

            addItem() {
                this.items.push({ project_id: '', product_name: '', unit: '', quantity: 1, price: 0, isFetching: false });
            },

            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },

            async fetchPrice(index) {
                let item = this.items[index];
                if (!item.project_id || !item.product_name) return;

                item.isFetching = true;
                try {
                    let url = `{{ route('api.latest-price') }}?project_id=${item.project_id}&product_name=${encodeURIComponent(item.product_name)}`;
                    let res = await fetch(url);
                    let data = await res.json();
                    
                    if (data.price) {
                        item.price = data.price;
                        // Optional: Show toast or highlight that price was suggested
                    }
                } catch (e) {
                    console.error("Failed to fetch price", e);
                } finally {
                    item.isFetching = false;
                }
            }
        }
    }
</script>
@endsection
