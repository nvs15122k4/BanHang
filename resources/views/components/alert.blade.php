@props(['type' => 'info', 'message', 'dismissible' => true])

@php
    $alertClass = match($type) {
        'success' => 'alert-success',
        'error', 'danger' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info',
        default => 'alert-info'
    };
    
    $icon = match($type) {
        'success' => 'fas fa-check-circle',
        'error', 'danger' => 'fas fa-exclamation-circle',
        'warning' => 'fas fa-exclamation-triangle',
        'info' => 'fas fa-info-circle',
        default => 'fas fa-info-circle'
    };
@endphp

<div class="alert {{ $alertClass }} {{ $dismissible ? 'alert-dismissible fade show' : '' }}" role="alert">
    <i class="{{ $icon }}"></i> {{ $message }}
    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>