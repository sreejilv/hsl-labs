@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'small text-danger mb-0 list-unstyled']) }}>
        @foreach ((array) $messages as $message)
            <li><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</li>
        @endforeach
    </ul>
@endif
