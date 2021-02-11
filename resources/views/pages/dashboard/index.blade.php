@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Dashboard</h1>
  </div>
  
  <div class="row">
    <div class="col-lg-4 col-md-4 col-sm-12">
      <div class="card card-statistic-2">
        <div class="card-stats">
          <div class="card-stats-title">
            Tujuan Pengaduan : Teknik
          </div>

          <div class="card-stats-items">
            <div class="card-stats-item">
              <div class="card-stats-item-count">24</div>
              <div class="card-stats-item-label">Baru</div>
            </div>

            <div class="card-stats-item">
              <div class="card-stats-item-count">12</div>
              <div class="card-stats-item-label">Menunggu</div>
            </div>

            <div class="card-stats-item">
              <div class="card-stats-item-count">24</div>
              <div class="card-stats-item-label">Selesai</div>
            </div>
            
          </div>

          <div class="card-icon shadow-primary bg-primary">
            <i class="fas fa-comments"></i>
          </div>

          <div class="card-wrap">
            <div class="card-header">
              <h4>Total Pengaduan</h4>
            </div>
            <div class="card-body">
              59
            </div>
          </div>

        </div>
      </div>
    </div>


    <div class="col-lg-4 col-md-4 col-sm-12">
      <div class="card card-statistic-2">
        <div class="card-stats">
          <div class="card-stats-title">
            Tujuan Pengaduan : Cleaning Service
          </div>

          <div class="card-stats-items">
            <div class="card-stats-item">
              <div class="card-stats-item-count">24</div>
              <div class="card-stats-item-label">Baru</div>
            </div>

            <div class="card-stats-item">
              <div class="card-stats-item-count">12</div>
              <div class="card-stats-item-label">Menunggu</div>
            </div>

            <div class="card-stats-item">
              <div class="card-stats-item-count">24</div>
              <div class="card-stats-item-label">Selesai</div>
            </div>
            
          </div>

          <div class="card-icon shadow-primary bg-primary">
            <i class="fas fa-comments"></i>
          </div>

          <div class="card-wrap">
            <div class="card-header">
              <h4>Total Pengaduan</h4>
            </div>
            <div class="card-body">
              59
            </div>
          </div>

        </div>
      </div>
    </div>


    <div class="col-lg-4 col-md-4 col-sm-12">
      <div class="card card-statistic-2">
        <div class="card-stats">
          <div class="card-stats-title">
            Tujuan Pengaduan : Security
          </div>

          <div class="card-stats-items">
            <div class="card-stats-item">
              <div class="card-stats-item-count">24</div>
              <div class="card-stats-item-label">Baru</div>
            </div>

            <div class="card-stats-item">
              <div class="card-stats-item-count">12</div>
              <div class="card-stats-item-label">Menunggu</div>
            </div>

            <div class="card-stats-item">
              <div class="card-stats-item-count">24</div>
              <div class="card-stats-item-label">Selesai</div>
            </div>
            
          </div>

          <div class="card-icon shadow-primary bg-primary">
            <i class="fas fa-comments"></i>
          </div>

          <div class="card-wrap">
            <div class="card-header">
              <h4>Total Pengaduan</h4>
            </div>
            <div class="card-body">
              59
            </div>
          </div>

        </div>
      </div>
    </div>

  </div>

</section>
@endsection

@push('scripts')
<script>
  $(function () {
    

  });
</script>
@endpush
