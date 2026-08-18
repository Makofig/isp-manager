@if ($errors->any())
    <div {{ $attributes }}>
        <div class="text-sm font-medium text-danger">{{ __('Whoops! Something went wrong.') }}</div>

        <ul class="mt-2 list-disc list-inside text-xs text-danger/80">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
