@props(['title', 'value', 'icon', 'color' => 'primary'])

<div class="col-md-3 mb-4">
    <div class="card text-white bg-{{ $color }}">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title mb-0">{{ $title }}</h6>
                    <h3 class="mb-0">{{ $value }}</h3>
                </div>
                <div class="text-white-50">
                    <i class="{{ $icon }} fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>