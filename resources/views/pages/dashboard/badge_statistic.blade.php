
@foreach ($results as $item)
<div class="col-lg-4 col-md-4 col-sm-12">
  <div class="card card-statistic-2">
    <div class="card-stats">
      <div class="card-stats-title">
        <small>
          <b>TUJUAN PENGADUAN : {{ strtoupper($item['name']) }}</b>
        </small>
      </div>

      <div class="card-stats-items">
        <div class="card-stats-item">
          <div class="card-stats-item-count">
            {{ $item['new'] }}
          </div>
          <div class="card-stats-item-label">Baru</div>
        </div>

        <div class="card-stats-item">
          <div class="card-stats-item-count">
            {{ $item['wait'] }}
          </div>
          <div class="card-stats-item-label">Menunggu</div>
        </div>

        <div class="card-stats-item">
          <div class="card-stats-item-count">
            {{ $item['finish'] }}
          </div>
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
          {{ $item['total'] }}
        </div>
      </div>

    </div>
  </div>
</div>
@endforeach
