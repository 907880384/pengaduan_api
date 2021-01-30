@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Notifikasi Pengaduan</h1>
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
                  <i>Pesan Pengaduan</i>
                </small>
              </div>
              <div class="author-box-description">
                <p>{{ $notification->data['data']['messages'] }}</p>
                <div class="text-black-50">
                  <small>
                    {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $notification->created_at)->format('d F Y, H:m:s') }}
                  </small>
                </div>
              </div>

            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>


@endsection
