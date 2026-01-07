@foreach ($exhibitors as $exhibitor)
    <div class="form-check mb-2">
        <input class="form-check-input recipient-checkbox" type="checkbox" value="{{ $exhibitor->id }}"
            data-id="{{ $exhibitor->id }}" data-type="exhibitor" id="exhibitor_{{ $exhibitor->id }}">
        <label class="form-check-label small" for="exhibitor_{{ $exhibitor->id }}">
            <div class="d-flex flex-column">
                <span class="fw-semibold">{{ $exhibitor->name }}</span>
                <small class="text-muted">{{ $exhibitor->email }} • {{ $exhibitor->location }}</small>
            </div>
        </label>
    </div>
@endforeach

<div class="d-flex justify-content-center mt-3">
    {{ $exhibitors->links() }}
</div>
