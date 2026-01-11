@foreach ($visitors as $visitor)
    <div class="form-check mb-2">
        <input class="form-check-input recipient-checkbox" type="checkbox" value="{{ $visitor->id }}"
            data-id="{{ $visitor->id }}" data-type="visitor" id="visitor_{{ $visitor->id }}">
        <label class="form-check-label small" for="visitor_{{ $visitor->id }}">
            <div class="d-flex flex-column">
                <span class="fw-semibold">{{ $visitor->name }}</span>
                <small class="text-muted">{{ $visitor->phone }} • {{ $visitor->location }}</small>
            </div>
        </label>
    </div>
@endforeach

<div class="d-flex justify-content-center mt-3">
    {{ $visitors->links() }}
</div>
