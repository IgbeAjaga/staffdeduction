@php $color = $color ?? 'primary'; @endphp
<div class="col-xl-3 col-md-6 col-12">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-0">{{ $title }}</h6>
                    <h2 class="fw-bolder mb-1 text-{{ $color }}">{{ $value }}</h2>
                    <a href="{{ $link }}" class="text-{{ $color }}">{{ $linkText }} →</a>
                </div>
                <div class="avatar bg-light-{{ $color }}">
                    <div class="avatar-content"><i data-feather="{{ $icon }}"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>