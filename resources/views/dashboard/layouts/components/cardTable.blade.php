<div class="card shadow">
  <div class="card-header border-0">
    <div class="row align-items-center">
      <div class="col">
        <h3 class="mb-0">{{ $title }}</h3>
      </div>
      <div class="col text-right">
        {{ $sideHeader }}
      </div>
    </div>
  </div>
  <div class="table-responsive">
    <!-- Projects table -->
    <table class="table align-items-center table-flush">
        {{ $slot }}
    </table>
  </div>
</div>
