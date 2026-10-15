@extends('layouts.mobile-customer')

@section('title', 'Tính khối lượng')
@section('heading', 'Tính khối lượng')

@section('content')
<div class="space-y-4 pb-8" x-data="vlxdCalc()">
    <p class="text-sm text-gray-600">Ước lượng nhanh tại công trường. Kết quả mang tính tham khảo — xác nhận với shop khi đặt.</p>

    <div class="flex gap-2 overflow-x-auto pb-1">
        <template x-for="tab in tabs" :key="tab.id">
            <button type="button" @click="active = tab.id"
                class="shrink-0 rounded-full px-4 py-2 text-sm font-bold mf-touch"
                :class="active === tab.id ? 'bg-primary text-white' : 'bg-white border border-gray-200 text-gray-600'"
                x-text="tab.label"></button>
        </template>
    </div>

    {{-- Bê tông --}}
    <div x-show="active === 'concrete'" x-cloak class="rounded-2xl bg-white border border-gray-200 p-4 space-y-3">
        <h2 class="font-bold">Bê tông (m³)</h2>
        <label class="block text-sm">
            <span class="font-semibold">Khối lượng bê tông (m³)</span>
            <input type="number" step="0.01" inputmode="decimal" x-model.number="concrete.m3" class="mt-1 w-full rounded-xl border-gray-300 mf-touch"/>
        </label>
        <label class="block text-sm">
            <span class="font-semibold">Cấp phối</span>
            <select x-model="concrete.mix" class="mt-1 w-full rounded-xl border-gray-300 mf-touch">
                <option value="m200">M200 (1 : 2 : 3)</option>
                <option value="m250">M250 (1 : 1.5 : 2.5)</option>
                <option value="m300">M300 (1 : 1 : 2)</option>
            </select>
        </label>
        <div class="rounded-xl bg-blue-50 p-3 text-sm space-y-1">
            <p>Xi măng ≈ <strong class="mf-price" x-text="format(cementBags) + ' bao (50kg)'"></strong></p>
            <p>Cát ≈ <strong x-text="format(sandM3) + ' m³'"></strong></p>
            <p>Đá ≈ <strong x-text="format(stoneM3) + ' m³'"></strong></p>
        </div>
        <button type="button" @click="orderFromConcrete()" class="mf-cta-btn mf-cta-btn--primary">Thêm vào đơn đặt hàng</button>
    </div>

    {{-- Gạch --}}
    <div x-show="active === 'brick'" x-cloak class="rounded-2xl bg-white border border-gray-200 p-4 space-y-3">
        <h2 class="font-bold">Gạch xây tường</h2>
        <label class="block text-sm">
            <span class="font-semibold">Diện tích tường (m²)</span>
            <input type="number" step="0.01" inputmode="decimal" x-model.number="brick.m2" class="mt-1 w-full rounded-xl border-gray-300 mf-touch"/>
        </label>
        <label class="block text-sm">
            <span class="font-semibold">Loại gạch</span>
            <select x-model="brick.type" class="mt-1 w-full rounded-xl border-gray-300 mf-touch">
                <option value="ong">Gạch ống (~64 viên/m²)</option>
                <option value="dac">Gạch đặc (~52 viên/m²)</option>
            </select>
        </label>
        <div class="rounded-xl bg-blue-50 p-3 text-sm">
            Cần ≈ <strong class="mf-price" x-text="format(brickCount) + ' viên'"></strong>
            <span class="text-gray-500">(+5% hao hụt)</span>
        </div>
        <button type="button" @click="orderFromBrick()" class="mf-cta-btn mf-cta-btn--primary">Thêm vào đơn đặt hàng</button>
    </div>

    {{-- Sơn / tô --}}
    <div x-show="active === 'plaster'" x-cloak class="rounded-2xl bg-white border border-gray-200 p-4 space-y-3">
        <h2 class="font-bold">Vữa tô / cát tô</h2>
        <label class="block text-sm">
            <span class="font-semibold">Diện tích (m²)</span>
            <input type="number" step="0.01" inputmode="decimal" x-model.number="plaster.m2" class="mt-1 w-full rounded-xl border-gray-300 mf-touch"/>
        </label>
        <label class="block text-sm">
            <span class="font-semibold">Độ dày (cm)</span>
            <input type="number" step="0.1" inputmode="decimal" x-model.number="plaster.cm" class="mt-1 w-full rounded-xl border-gray-300 mf-touch"/>
        </label>
        <div class="rounded-xl bg-blue-50 p-3 text-sm space-y-1">
            <p>Thể tích vữa ≈ <strong x-text="format(plasterM3) + ' m³'"></strong></p>
            <p>Cát tô ≈ <strong x-text="format(plasterSand) + ' m³'"></strong></p>
            <p>Xi măng ≈ <strong class="mf-price" x-text="format(plasterCement) + ' bao'"></strong></p>
        </div>
        <button type="button" @click="orderFromPlaster()" class="mf-cta-btn mf-cta-btn--primary">Thêm vào đơn đặt hàng</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
function vlxdCalc() {
    const createUrl = @js(route('portal.orders.create'));
    // Mix ratios approx bags cement / m3 + sand/stone m3 per m3 concrete
    const mixes = {
        m200: { cement: 6.5, sand: 0.45, stone: 0.85 },
        m250: { cement: 7.5, sand: 0.40, stone: 0.80 },
        m300: { cement: 8.5, sand: 0.35, stone: 0.75 },
    };
    return {
        active: 'concrete',
        tabs: [
            { id: 'concrete', label: 'Bê tông' },
            { id: 'brick', label: 'Gạch' },
            { id: 'plaster', label: 'Vữa tô' },
        ],
        concrete: { m3: 1, mix: 'm200' },
        brick: { m2: 10, type: 'ong' },
        plaster: { m2: 20, cm: 1.5 },
        format(n) {
            return new Intl.NumberFormat('vi-VN', { maximumFractionDigits: 2 }).format(n || 0);
        },
        get cementBags() {
            const m = mixes[this.concrete.mix];
            return Math.ceil((this.concrete.m3 || 0) * m.cement);
        },
        get sandM3() {
            return (this.concrete.m3 || 0) * mixes[this.concrete.mix].sand;
        },
        get stoneM3() {
            return (this.concrete.m3 || 0) * mixes[this.concrete.mix].stone;
        },
        get brickCount() {
            const per = this.brick.type === 'dac' ? 52 : 64;
            return Math.ceil((this.brick.m2 || 0) * per * 1.05);
        },
        get plasterM3() {
            return (this.plaster.m2 || 0) * ((this.plaster.cm || 0) / 100);
        },
        get plasterSand() {
            return this.plasterM3 * 1.1;
        },
        get plasterCement() {
            return Math.ceil(this.plasterM3 * 5);
        },
        goOrder(items) {
            sessionStorage.setItem('portal_calc_items', JSON.stringify(items));
            window.location.href = createUrl + (createUrl.includes('?') ? '&' : '?') + 'from_calc=1';
        },
        orderFromConcrete() {
            this.goOrder([
                { product_name: 'Xi măng PCB40', unit: 'bao', quantity: this.cementBags, price: 0 },
                { product_name: 'Cát vàng', unit: 'm3', quantity: Math.round(this.sandM3 * 100) / 100, price: 0 },
                { product_name: 'Đá 1x2', unit: 'm3', quantity: Math.round(this.stoneM3 * 100) / 100, price: 0 },
            ]);
        },
        orderFromBrick() {
            this.goOrder([
                {
                    product_name: this.brick.type === 'dac' ? 'Gạch đặc' : 'Gạch ống 4 lỗ',
                    unit: 'viên',
                    quantity: this.brickCount,
                    price: 0,
                },
            ]);
        },
        orderFromPlaster() {
            this.goOrder([
                { product_name: 'Cát tô', unit: 'm3', quantity: Math.round(this.plasterSand * 100) / 100, price: 0 },
                { product_name: 'Xi măng PCB40', unit: 'bao', quantity: this.plasterCement, price: 0 },
            ]);
        },
    };
}
</script>
@endpush
