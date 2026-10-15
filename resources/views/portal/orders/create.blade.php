@extends('layouts.mobile-customer')

@section('title', 'Đặt hàng')
@section('heading', 'Đặt hàng 3 bước')

@section('content')
<div
    class="space-y-4 pb-28"
    x-data="orderWizard(@js([
        'project_id' => $selectedProjectId,
        'delivery_address' => old('delivery_address', $projects->firstWhere('id', $selectedProjectId)?->address),
        'note' => old('note'),
        'items' => old('items', $reorder?->items->map(fn ($i) => [
            'product_name' => $i->product_name,
            'unit' => $i->unit,
            'quantity' => (float) $i->quantity,
            'price' => (float) $i->price,
            'price_hint' => null,
            'price_loading' => false,
            'suggestions' => [],
            'show_suggestions' => false,
        ])->values()->all() ?: [[
            'product_name' => '',
            'unit' => 'cái',
            'quantity' => 1,
            'price' => 0,
            'price_hint' => null,
            'price_loading' => false,
            'suggestions' => [],
            'show_suggestions' => false,
        ]]),
        'priceUrl' => route('portal.orders.latest-price'),
        'suggestUrl' => route('portal.orders.suggest'),
        'chipSuggestions' => $suggestions->map(fn ($s) => [
            'product_name' => $s->product_name,
            'unit' => $s->unit,
            'price' => (float) $s->price,
        ])->values()->all(),
    ]))"
>
    <div class="flex gap-2 text-xs font-bold">
        <template x-for="s in 3" :key="s">
            <button type="button" @click="step = s" class="flex-1 rounded-full py-2 mf-touch"
                :class="step === s ? 'bg-primary text-white' : 'bg-white border border-gray-200 text-gray-500'">
                Bước <span x-text="s"></span>
            </button>
        </template>
    </div>

    <form method="POST" action="{{ route('portal.orders.store') }}" @submit="if (step < 3) { $event.preventDefault(); next() }">
        @csrf

        {{-- Step 1: Project --}}
        <div x-show="step === 1" x-cloak class="rounded-2xl bg-white border border-gray-200 p-4 space-y-4">
            <h2 class="font-bold text-lg">1. Chọn công trình</h2>
            <label class="block">
                <span class="text-sm font-semibold mb-1 block">Công trình</span>
                <select name="project_id" x-model="form.project_id" @change="onProjectChange()" class="w-full rounded-xl border-gray-300 mf-touch">
                    <option value="">— Giao địa chỉ khác —</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" @selected($project->id == $selectedProjectId)>{{ $project->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block">
                <span class="text-sm font-semibold mb-1 block">Địa chỉ giao</span>
                <input type="text" name="delivery_address" x-model="form.delivery_address" class="w-full rounded-xl border-gray-300 mf-touch" placeholder="Địa chỉ công trường"/>
            </label>
            <p class="text-xs text-gray-500">Giá realtime lấy theo lịch sử công trình đã chọn (hóa đơn / đơn trước).</p>
            <button type="button" @click="next()" class="mf-cta-btn mf-cta-btn--primary">Tiếp: chọn vật tư</button>
        </div>

        {{-- Step 2: Items --}}
        <div x-show="step === 2" x-cloak class="space-y-3">
            <div class="rounded-2xl bg-white border border-gray-200 p-4">
                <h2 class="font-bold text-lg mb-3">2. Thêm vật tư</h2>

                <template x-if="chipSuggestions.length">
                    <div class="mb-4">
                        <p class="text-xs text-gray-500 mb-2">Gợi ý từ lịch sử</p>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="(s, si) in chipSuggestions" :key="si">
                                <button type="button"
                                    @click="addSuggestion(s.product_name, s.unit, s.price)"
                                    class="text-xs font-semibold rounded-full px-3 py-2 bg-blue-50 text-primary mf-touch"
                                    x-text="s.product_name + ' · ' + formatMoney(s.price)"></button>
                            </template>
                        </div>
                    </div>
                </template>

                <template x-for="(item, idx) in form.items" :key="idx">
                    <div class="border border-gray-100 rounded-xl p-3 mb-3 space-y-2 relative">
                        <div class="relative">
                            <input type="text" :name="`items[${idx}][product_name]`" x-model="item.product_name"
                                @input.debounce.350ms="onProductInput(idx)"
                                @focus="item.show_suggestions = item.suggestions?.length > 0"
                                @blur="setTimeout(() => item.show_suggestions = false, 180)"
                                placeholder="Gõ tên vật tư (xi măng, cát...)"
                                autocomplete="off"
                                class="w-full rounded-lg border-gray-300 mf-touch" required/>

                            <div x-show="item.show_suggestions && item.suggestions?.length"
                                 x-cloak
                                 class="absolute z-20 left-0 right-0 mt-1 rounded-xl border border-gray-200 bg-white shadow-lg max-h-48 overflow-y-auto">
                                <template x-for="(sug, si) in item.suggestions" :key="si">
                                    <button type="button"
                                        class="w-full text-left px-3 py-2.5 text-sm hover:bg-blue-50 border-b border-gray-50 last:border-0"
                                        @mousedown.prevent="applySuggestion(idx, sug)">
                                        <span class="font-semibold" x-text="sug.product_name"></span>
                                        <span class="text-gray-500 text-xs block">
                                            <span x-text="sug.unit"></span> ·
                                            <span class="mf-price text-primary" x-text="formatMoney(sug.price) + ' đ'"></span>
                                        </span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <input type="text" :name="`items[${idx}][unit]`" x-model="item.unit" placeholder="ĐVT" class="rounded-lg border-gray-300 mf-touch" required/>
                            <input type="number" step="0.01" inputmode="decimal" :name="`items[${idx}][quantity]`" x-model.number="item.quantity" placeholder="SL" class="rounded-lg border-gray-300 mf-touch" required/>
                            <input type="number" step="1" inputmode="numeric" :name="`items[${idx}][price]`" x-model.number="item.price" placeholder="Giá" class="rounded-lg border-gray-300 mf-touch"/>
                        </div>

                        <div class="min-h-[1.25rem] text-xs">
                            <span x-show="item.price_loading" class="text-gray-400">Đang lấy giá lần trước…</span>
                            <span x-show="!item.price_loading && item.price_hint" class="text-primary font-semibold" x-text="item.price_hint"></span>
                            <span x-show="!item.price_loading && item.price_hint === false" class="text-amber-700">Chưa có giá lịch sử — shop sẽ báo khi xác nhận.</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <p class="text-sm font-bold">
                                Dòng: <span class="mf-price text-primary" x-text="formatMoney((item.quantity||0)*(item.price||0)) + ' đ'"></span>
                            </p>
                            <button type="button" @click="removeItem(idx)" class="text-sm text-red-600 font-semibold mf-touch" x-show="form.items.length > 1">Xóa</button>
                        </div>
                    </div>
                </template>

                <button type="button" @click="addItem()" class="w-full rounded-xl border border-dashed border-primary text-primary font-bold py-3 mf-touch">+ Thêm dòng</button>
            </div>
            <div class="rounded-xl bg-white border border-gray-200 px-4 py-3 flex items-center justify-between">
                <span class="text-sm text-gray-500">Tạm tính</span>
                <span class="mf-price text-lg text-primary" x-text="formatMoney(total) + ' đ'"></span>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <button type="button" @click="step = 1" class="mf-cta-btn mf-cta-btn--secondary">Quay lại</button>
                <button type="button" @click="next()" class="mf-cta-btn mf-cta-btn--primary">Tiếp: gửi đơn</button>
            </div>
        </div>

        {{-- Step 3: Confirm --}}
        <div x-show="step === 3" x-cloak class="rounded-2xl bg-white border border-gray-200 p-4 space-y-4">
            <h2 class="font-bold text-lg">3. Xác nhận gửi</h2>
            <div class="text-sm space-y-2">
                <p><span class="text-gray-500">Địa chỉ:</span> <span x-text="form.delivery_address || '—'"></span></p>
                <ul class="divide-y border rounded-xl overflow-hidden">
                    <template x-for="(item, idx) in form.items.filter(i => i.product_name)" :key="idx">
                        <li class="px-3 py-2 flex justify-between gap-2 bg-gray-50">
                            <span>
                                <span class="font-semibold" x-text="item.product_name"></span>
                                <span class="text-xs text-gray-500 block" x-text="item.quantity + ' ' + item.unit + ' × ' + formatMoney(item.price)"></span>
                            </span>
                            <span class="font-bold whitespace-nowrap" x-text="formatMoney((item.quantity||0)*(item.price||0)) + ' đ'"></span>
                        </li>
                    </template>
                </ul>
                <p class="mf-price text-lg">Tạm tính: <span x-text="formatMoney(total)"></span> đ</p>
                <p class="text-xs text-amber-700">Giá tham khảo từ lịch sử; cửa hàng có thể điều chỉnh khi xác nhận.</p>
            </div>
            <label class="block">
                <span class="text-sm font-semibold mb-1 block">Ghi chú</span>
                <textarea name="note" x-model="form.note" rows="2" class="w-full rounded-xl border-gray-300" placeholder="Giao sáng / cần xe cẩu..."></textarea>
            </label>
            <div class="grid grid-cols-2 gap-3">
                <button type="button" @click="step = 2" class="mf-cta-btn mf-cta-btn--secondary">Quay lại</button>
                <button type="submit" class="mf-cta-btn mf-cta-btn--primary">Gửi yêu cầu</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function orderWizard(initial) {
    const projects = @js($projects->map(fn ($p) => ['id' => $p->id, 'address' => $p->address])->values());
    const emptyItem = () => ({
        product_name: '',
        unit: 'cái',
        quantity: 1,
        price: 0,
        price_hint: null,
        price_loading: false,
        suggestions: [],
        show_suggestions: false,
    });

    // Normalize old() items missing UI fields
    (initial.items || []).forEach((item) => {
        item.price_hint = item.price_hint ?? null;
        item.price_loading = false;
        item.suggestions = item.suggestions || [];
        item.show_suggestions = false;
    });

    return {
        step: 1,
        form: initial,
        chipSuggestions: initial.chipSuggestions || [],
        get total() {
            return this.form.items.reduce((s, i) => s + (Number(i.quantity) || 0) * (Number(i.price) || 0), 0);
        },
        formatMoney(n) {
            return new Intl.NumberFormat('vi-VN').format(Math.round(n || 0));
        },
        formatDate(d) {
            if (!d) return '';
            try {
                const dt = new Date(d);
                if (Number.isNaN(dt.getTime())) return String(d).slice(0, 10);
                return dt.toLocaleDateString('vi-VN');
            } catch (e) {
                return '';
            }
        },
        next() {
            if (this.step === 2 && this.form.items.every(i => !String(i.product_name || '').trim())) {
                alert('Thêm ít nhất một vật tư');
                return;
            }
            if (this.step < 3) this.step++;
        },
        init() {
            try {
                const raw = sessionStorage.getItem('portal_calc_items');
                if (raw && new URLSearchParams(window.location.search).get('from_calc')) {
                    const calcItems = JSON.parse(raw);
                    if (Array.isArray(calcItems) && calcItems.length) {
                        this.form.items = calcItems.map((i) => ({
                            ...emptyItem(),
                            product_name: i.product_name || '',
                            unit: i.unit || 'cái',
                            quantity: Number(i.quantity) || 1,
                            price: Number(i.price) || 0,
                        }));
                        this.step = 2;
                        sessionStorage.removeItem('portal_calc_items');
                        this.form.items.forEach((_, idx) => this.fetchPrice(idx));
                    }
                }
            } catch (e) {}
        },
        addItem() {
            this.form.items.push(emptyItem());
        },
        removeItem(idx) {
            this.form.items.splice(idx, 1);
        },
        addSuggestion(name, unit, price) {
            const item = emptyItem();
            item.product_name = name;
            item.unit = unit || 'cái';
            item.price = price || 0;
            item.price_hint = price
                ? `Giá lần trước: ${this.formatMoney(price)} đ`
                : false;
            // Replace empty first row if still blank
            if (this.form.items.length === 1 && !this.form.items[0].product_name) {
                this.form.items[0] = item;
            } else {
                this.form.items.push(item);
            }
            this.step = 2;
        },
        applySuggestion(idx, sug) {
            const item = this.form.items[idx];
            item.product_name = sug.product_name;
            item.unit = sug.unit || item.unit || 'cái';
            item.price = sug.price || 0;
            item.price_hint = `Giá lần trước: ${this.formatMoney(sug.price)} đ` +
                (sug.priced_at ? ` (${this.formatDate(sug.priced_at)})` : '');
            item.suggestions = [];
            item.show_suggestions = false;
        },
        async onProjectChange() {
            const p = projects.find(x => String(x.id) === String(this.form.project_id));
            if (p && p.address) this.form.delivery_address = p.address;
            await this.reloadChips();
            // Refresh prices for typed lines
            this.form.items.forEach((_, idx) => {
                if (this.form.items[idx].product_name) this.fetchPrice(idx);
            });
        },
        async reloadChips() {
            try {
                const url = new URL(this.form.suggestUrl, window.location.origin);
                if (this.form.project_id) url.searchParams.set('project_id', this.form.project_id);
                const res = await fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
                const data = await res.json();
                this.chipSuggestions = data.data || [];
            } catch (e) {}
        },
        async onProductInput(idx) {
            const item = this.form.items[idx];
            const q = String(item.product_name || '').trim();
            if (q.length < 1) {
                item.suggestions = [];
                item.show_suggestions = false;
                item.price_hint = null;
                return;
            }
            await Promise.all([
                this.fetchSuggestions(idx),
                this.fetchPrice(idx),
            ]);
        },
        async fetchSuggestions(idx) {
            const item = this.form.items[idx];
            const q = String(item.product_name || '').trim();
            try {
                const url = new URL(this.form.suggestUrl, window.location.origin);
                url.searchParams.set('q', q);
                if (this.form.project_id) url.searchParams.set('project_id', this.form.project_id);
                const res = await fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
                const data = await res.json();
                item.suggestions = data.data || [];
                item.show_suggestions = item.suggestions.length > 0;
            } catch (e) {
                item.suggestions = [];
            }
        },
        async fetchPrice(idx) {
            const item = this.form.items[idx];
            const name = String(item.product_name || '').trim();
            if (!name) return;

            item.price_loading = true;
            item.price_hint = null;
            try {
                const url = new URL(this.form.priceUrl, window.location.origin);
                if (this.form.project_id) url.searchParams.set('project_id', this.form.project_id);
                url.searchParams.set('product_name', name);
                const res = await fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
                const data = await res.json();
                if (data.price != null) {
                    // Chỉ auto-fill nếu user chưa sửa giá tay (0 hoặc trống)
                    if (!item.price || Number(item.price) === 0) {
                        item.price = data.price;
                    }
                    if (data.unit) item.unit = data.unit;
                    const src = data.source === 'order' ? 'đơn trước' : 'hóa đơn';
                    item.price_hint = `Giá lần trước (${src}): ${this.formatMoney(data.price)} đ` +
                        (data.priced_at ? ` — ${this.formatDate(data.priced_at)}` : '') +
                        (data.product_name && data.product_name !== name ? ` · khớp “${data.product_name}”` : '');
                } else {
                    item.price_hint = false;
                }
            } catch (e) {
                item.price_hint = false;
            } finally {
                item.price_loading = false;
            }
        }
    }
}
</script>
@endpush
