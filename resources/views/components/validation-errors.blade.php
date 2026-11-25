{{-- $errors chỉ có khi đi qua middleware web (ShareErrorsFromSession); render ngoài request thì bỏ qua. --}}
@if(isset($errors) && $errors->any())
    <div role="alert" class="mb-4 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-800 print:hidden">
        <ul class="list-disc space-y-1 pl-5">
            @foreach($errors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif
