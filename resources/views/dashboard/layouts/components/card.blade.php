<div class="col-xl-3 col-lg-6">
  <div class="card card-stats mb-4 mb-xl-0">
    <div class="card-body">
      <div class="row">
        <div class="col">
          <h5 class="card-title text-uppercase text-muted mb-0">{{ $title }}</h5>
          <span class="h2 font-weight-bold mb-0">{{ $count }}</span>
        </div>
        <div class="col-auto">
          <div class="icon icon-shape bg-{{ $iconTheme }} text-white rounded-circle shadow">
            <i class="fas fa-{{ $icon }}"></i>
          </div>
        </div>
      </div>
      <p class="mt-3 mb-0 text-muted text-sm">
        {{ $slot }}
      </p>
    </div>
  </div>
</div>
