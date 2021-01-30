@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Form Penyelesaian Aktivitas</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item">
        <a href="{{ url('activities') }}">Kembali</a>
      </div>
    </div>
  </div>

  <div class="section-body">
    <p class="section-title">
      Silahkan isi keterangan, setelah pekerjaan anda selesai
    </p>
    <div class="row">
      <div class="col-12 col-sm-12 col-lg-6">
        <div class="card">
          <div class="card-header">
            <h6 class="text-info">
              {{ $complaint->typeComplaint->title }}
            </h6>
          </div>
          <div class="card-body">
            <div class="text-default">
              Pesan Pengaduan <i class="fa fa-arrow-right"></i> {{ $complaint->messages }}
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
  <script></script>
@endpush