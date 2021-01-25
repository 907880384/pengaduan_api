@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Notifikasi Penugasan Pengaduan</h1>
  </div>


  <div class="section-body">
    <h2 class="section-title">Baca Notifikasi</h2>

    <div class="row">
      <div class="col-12 col-sm-12 col-lg-12">
        <div class="card author-box card-primary">
          <div class="card-body">
            <div class="author-box-details">
              <div class="author-box-name">
                <h5 class="text-primary">
                  <strong>
                    {{ $notification->data['message'] }}
                  </strong>
                </h5>
              </div>

              <div class="author-box-job">
                <small>
                  <i>
                    {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->created_at)->format('d F Y, H:m:s') }}
                  </i>
                </small>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>


@endsection
