@extends('layouts.mobile-customer')

@section('title', $order->code)
@section('heading', $order->code)

@section('bottom_nav')
@endsection

@section('content')
<div class="space-y-4 pb-8">
    <div class="rounded-2xl bg-white border p-4 text-sm space-y-1">
        <p class="font-bold text-lg">{{ $order->customer?->name }}</p>
        <p>{{ $order->delivery_address }}</p>
        @if($order->customer?->phone)
            <a href="tel:{{ preg_replace('/\D+/', '', $order->customer->phone) }}" class="inline-flex text-primary font-bold mf-touch mt-2">Gọi khách</a>
        @endif
    </div>

    <ul class="rounded-2xl bg-white border divide-y">
        @foreach($order->items as $item)
            <li class="px-4 py-3 text-sm flex justify-between">
                <span>{{ $item->product_name }}</span>
                <span class="font-bold">{{ number_format($item->quantity, 2, ',', '.') }} {{ $item->unit }}</span>
            </li>
        @endforeach
    </ul>

    @if($order->status === 'confirmed')
        <form method="POST" action="{{ route('driver.start', $order) }}">
            @csrf
            <button class="mf-cta-btn mf-cta-btn--primary">Bắt đầu giao</button>
        </form>
    @endif

    @if($order->status === 'delivering')
        <form method="POST" action="{{ route('driver.complete', $order) }}" enctype="multipart/form-data" class="space-y-3" id="complete-form">
            @csrf
            <label class="block rounded-2xl bg-white border p-4">
                <span class="text-sm font-semibold block mb-2">Ảnh biên nhận (tuỳ chọn)</span>
                <input type="file" name="delivery_photo" accept="image/*" capture="environment" class="w-full text-sm"/>
            </label>
            <input type="hidden" name="delivery_lat" id="delivery_lat"/>
            <input type="hidden" name="delivery_lng" id="delivery_lng"/>
            <button class="mf-cta-btn mf-cta-btn--primary">Đã giao xong</button>
        </form>
    @endif

    <a href="{{ route('driver.index') }}" class="block text-center text-sm font-bold text-primary">← Quay lại</a>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('complete-form')?.addEventListener('submit', function (e) {
    if (!navigator.geolocation || this.dataset.geoDone) return;
    e.preventDefault();
    const form = this;
    const finish = () => { form.dataset.geoDone = '1'; form.submit(); };
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            document.getElementById('delivery_lat').value = pos.coords.latitude;
            document.getElementById('delivery_lng').value = pos.coords.longitude;
            finish();
        },
        () => finish(),
        { timeout: 5000 }
    );
});
</script>
@endpush
