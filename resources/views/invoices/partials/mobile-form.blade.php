{{--
    Form hóa đơn mobile dùng chung cho tạo + sửa (trước là 2 file gần giống nhau, dễ lệch).
    Biến: $formId, $action, $method ('POST'|'PUT'), $cancelUrl, $submitLabel,
          $invoice (null khi tạo), $initialItems (mảng {product_name, unit, quantity, price}).
--}}
@php
    $invoiceDate = $invoice?->invoice_date
        ? \Illuminate\Support\Carbon::parse($invoice->invoice_date)->format('Y-m-d')
        : now()->format('Y-m-d');
@endphp

<div x-data="invoiceForm(@js($initialItems))" class="pb-28">
    <form id="{{ $formId }}" action="{{ $action }}" method="POST" @submit="isLoading = true" class="flex flex-col gap-4">
        @csrf
        @if($method === 'PUT')
            <input type="hidden" name="_method" value="PUT">
        @endif

        {{-- Thông tin chung --}}
        <section class="space-y-4 rounded-xl border border-border-light bg-container-light p-4 shadow-subtle dark:border-border-dark dark:bg-container-dark">
            <div>
                <label for="invoice-date" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Ngày xuất</label>
                <input id="invoice-date" type="date" name="invoice_date" value="{{ old('invoice_date', $invoiceDate) }}" required x-vietnamese-date
                    class="w-full rounded-lg border-border-light bg-background-light p-3 text-sm focus:border-primary focus:ring-primary dark:border-border-dark dark:bg-background-dark">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Mã hóa đơn <span class="text-gray-400">(tùy chọn)</span></label>
                <input type="text" name="code" value="{{ $invoice?->code }}" placeholder="VD: HD-001"
                    class="w-full rounded-lg border-border-light bg-background-light p-3 text-sm focus:border-primary focus:ring-primary dark:border-border-dark dark:bg-background-dark">
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Ghi chú</label>
                <textarea name="note" rows="2" placeholder="Ghi chú về đơn hàng"
                    class="w-full rounded-lg border-border-light bg-background-light p-3 text-sm focus:border-primary focus:ring-primary dark:border-border-dark dark:bg-background-dark">{{ $invoice?->note }}</textarea>
            </div>
        </section>

        {{-- Dòng vật liệu --}}
        <section class="space-y-3">
            <div class="flex items-center justify-between px-1">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Danh sách sản phẩm</h2>
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500" x-text="`${items.length} mục`">0 mục</span>
            </div>

            <template x-for="(item, index) in items" :key="index">
                <div class="relative space-y-3 rounded-xl border border-border-light bg-container-light p-4 shadow-subtle dark:border-border-dark dark:bg-container-dark">
                    <button type="button" @click="removeItem(index)" x-show="items.length > 1" aria-label="Xóa dòng"
                        class="absolute right-2 top-2 flex h-11 w-11 items-center justify-center rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20">
                        <span class="material-symbols-outlined text-xl">delete</span>
                    </button>

                    <div class="pr-10">
                        <label class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400">Tên sản phẩm</label>
                        <input type="text" :name="`items[${index}][product_name]`" x-model="item.product_name" required placeholder="Nhập tên sản phẩm"
                            class="w-full rounded-lg border-border-light bg-background-light p-2.5 text-sm font-semibold focus:border-primary focus:ring-primary dark:border-border-dark dark:bg-background-dark">
                    </div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400">
                        Ngày giao hàng
                        <input type="date" :name="'items[' + index + '][date]'" x-model="item.date" x-vietnamese-date="item.date"
                            class="mt-1 w-full rounded-lg border-border-light bg-background-light p-2.5 text-sm dark:border-border-dark dark:bg-background-dark">
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400">Đơn vị</label>
                            <input type="text" :name="`items[${index}][unit]`" x-model="item.unit" placeholder="kg, m3…"
                                class="w-full rounded-lg border-border-light bg-background-light p-2.5 text-sm focus:border-primary focus:ring-primary dark:border-border-dark dark:bg-background-dark">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-500 dark:text-gray-400">Số lượng</label>
                            <input type="number" step="any" inputmode="decimal" :name="`items[${index}][quantity]`" x-model.number="item.quantity" required placeholder="0"
                                class="w-full rounded-lg border-border-light bg-background-light p-2.5 text-center text-sm focus:border-primary focus:ring-primary dark:border-border-dark dark:bg-background-dark">
                        </div>
                        <div>
                            <label class="mb-1 block text-right text-xs font-semibold text-gray-500 dark:text-gray-400">Đơn giá</label>
                            <input type="number" step="any" inputmode="decimal" :name="`items[${index}][price]`" x-model.number="item.price" required placeholder="0"
                                class="w-full rounded-lg border-border-light bg-background-light p-2.5 text-right text-sm focus:border-primary focus:ring-primary dark:border-border-dark dark:bg-background-dark">
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2 text-right">
                        <span class="text-xs font-semibold uppercase text-gray-400">Thành tiền</span>
                        <span class="text-sm font-bold text-primary" x-text="formatNumber(item.quantity * item.price) + 'đ'"></span>
                    </div>
                </div>
            </template>

            <button type="button" @click="addItem()" class="flex min-h-[48px] w-full items-center justify-center gap-2 rounded-xl border-2 border-dashed border-gray-300 font-bold text-primary hover:bg-primary/5 dark:border-gray-700">
                <span class="material-symbols-outlined">add_circle</span>
                Thêm sản phẩm
            </button>
        </section>

        {{-- Tổng tiền --}}
        <section class="space-y-2 rounded-xl border border-border-light bg-container-light p-4 shadow-subtle dark:border-border-dark dark:bg-container-dark">
            @if($invoice?->total_text)
                <p class="text-right text-xs italic text-gray-400">({{ $invoice->total_text }})</p>
            @endif
            <div class="flex items-center justify-between">
                <span class="text-base font-bold text-gray-900 dark:text-white">Tổng cộng</span>
                <span class="text-xl font-black text-primary" x-text="formatNumber(totalAmount) + 'đ'">0đ</span>
            </div>
        </section>
    </form>

    {{-- Thanh lưu dính đáy --}}
    <div class="fixed inset-x-0 bottom-0 z-30 border-t border-border-light bg-container-light/95 p-4 pb-[calc(1rem+env(safe-area-inset-bottom))] backdrop-blur-md dark:border-border-dark dark:bg-container-dark/95 lg:left-64">
        <div class="mx-auto flex max-w-2xl gap-3">
            <a href="{{ $cancelUrl }}" class="flex min-h-[48px] flex-1 items-center justify-center rounded-lg bg-gray-100 text-sm font-bold text-gray-700 dark:bg-gray-800 dark:text-gray-300">Hủy</a>
            <button type="submit" form="{{ $formId }}" class="flex min-h-[48px] flex-[2] items-center justify-center rounded-lg bg-primary text-sm font-bold text-white shadow-lg shadow-primary/25">{{ $submitLabel }}</button>
        </div>
    </div>
</div>

<script>
    function invoiceForm(initialItems) {
        return {
            items: initialItems.length
                ? initialItems.map((item) => ({
                    date: item.date ?? '',
                    product_name: item.product_name ?? '',
                    unit: item.unit ?? '',
                    quantity: parseFloat(item.quantity) || 0,
                    price: parseFloat(item.price) || 0,
                }))
                : [{ product_name: '', date: '', unit: '', quantity: 1, price: 0 }],

            addItem() {
                this.items.push({ product_name: '', date: '', unit: '', quantity: 1, price: 0 });
            },

            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },

            get totalAmount() {
                return this.items.reduce((sum, item) => sum + (item.quantity * item.price), 0);
            },

            formatNumber(num) {
                return new Intl.NumberFormat('vi-VN').format(num);
            },
        };
    }
</script>
