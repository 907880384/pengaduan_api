
<!-- PENGADUAN -->
@if (strtolower(auth()->user()->roles()->first()->slug) != 'receptionis')
  <div class="col-lg-4 col-md-4 col-sm-12 col-12">
    <div class="card card-statistic-1">
      <div class="card-icon bg-secondary">
        <i class="fas fa-folder-open"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Pengaduan Baru</h4>
        </div>
        <div class="card-body">
          {{ $results['new_complaint'] }}
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-4 col-sm-12 col-12">
    <div class="card card-statistic-1">
      <div class="card-icon bg-primary">
        <i class="fas fa-clipboard-list"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Pengaduan Selesai</h4>
        </div>
        <div class="card-body">
          {{ $results['finish_complaint'] }}
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-4 col-sm-12 col-12">
    <div class="card card-statistic-1">
      <div class="card-icon bg-info">
        <i class="fas fa-calculator"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Total Pengaduan</h4>
        </div>
        <div class="card-body">
          {{ $results["total_complaint"] }}
        </div>
      </div>
    </div>
  </div>
@endif

<!-- TAMU -->
@if (strtolower(auth()->user()->roles()->first()->slug ) == 'admin' || strtolower(auth()->user()->roles()->first()->slug) == 'receptionis')
  <div class="col-lg-4 col-md-4 col-sm-12 col-12">
    <div class="card card-statistic-1">
      <div class="card-icon bg-warning">
        <i class="fas fa-sign-in-alt"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Tamu Masuk</h4>
        </div>
        <div class="card-body">
          {{ $results["visitor_come"] }}
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-4 col-sm-12 col-12">
    <div class="card card-statistic-1">
      <div class="card-icon bg-success">
        <i class="fas fa-sign-out-alt"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Tamu Keluar</h4>
        </div>
        <div class="card-body">
          {{ $results["visitor_exit"] }}
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-4 col-sm-12 col-12">
    <div class="card card-statistic-1">
      <div class="card-icon bg-info">
        <i class="fas fa-calculator"></i>
      </div>
      <div class="card-wrap">
        <div class="card-header">
          <h4>Total Tamu</h4>
        </div>
        <div class="card-body">
          {{ $results["total_visitor"] }}
        </div>
      </div>
    </div>
  </div>    
@endif


