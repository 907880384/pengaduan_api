@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Detail Pengaduan</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item">
        <a href="{{ url('/complaints') }}">Kembali</a>
      </div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">
      <div class="col-12 col-lg-8 col-md-8 col-sm-12">
        <div class="card author-box card-primary">
          <div class="card-body">
            
            

            <div class="author-box-details">
              <div class="author-box-name">
                <a href="#">{{ $record->title }}</a>
              </div>

              <div class="author-box-job">
                {!! $record->is_urgent ? '<span class="text-success">Perihal Penting</span>' : 'Perihal Biasa' !!}
              </div>

              <div class="author-box-description">
                <p>{{ $record->messages }}</p>
              </div>

              <div class="mb-2 mt-3">
                <div class="text-small text-info font-weight-bold">
                  {{ 'Tujuan ' . $record->types->name }}
                </div>
              </div>
            </div>

          </div>

          

        </div>
      </div>


      <div class="col-12 col-md-4 col-lg-4 col-sm-12">
        <div class="pricing pricing-highlight">
          <div class="pricing-title">
            Keterangan Pengaduan
          </div>
          <div class="pricing-padding">
            <div class="pricing-details">
              {{-- ASSIGNED --}}
              @if ($record->is_assigned)
              <div class="pricing-item">
                <div class="pricing-item-icon">
                  <i class="fas fa-check"></i>
                </div>
                <div class="pricing-item-label">
                  Pengaduan ditugaskan
                </div>
              </div>
              @else
              <div class="pricing-item">
                <div class="pricing-item-icon bg-danger text-white">
                  <i class="fas fa-times"></i>
                </div>
                <div class="pricing-item-label">
                  Pengaduan menunggu ditugaskan
                </div>
              </div>
              @endif

              {{-- ASSIGNED --}}
              @if ($record->assigned != null && $record->assigned->is_accepted)
              <div class="pricing-item">
                <div class="pricing-item-icon">
                  <i class="fas fa-check"></i>
                </div>
                <div class="pricing-item-label">
                  Pekerjaan telah diambil petugas
                </div>
              </div>
              @else
              <div class="pricing-item">
                <div class="pricing-item-icon bg-danger text-white">
                  <i class="fas fa-times"></i>
                </div>
                <div class="pricing-item-label">
                  Pekerjaan belum diambil petugas
                </div>
              </div>
              @endif


              {{-- FINISHED --}}
              @if ($record->is_finished)
              <div class="pricing-item">
                <div class="pricing-item-icon">
                  <i class="fas fa-check"></i>
                </div>
                <div class="pricing-item-label">
                  Pekerjaan sudah selesai
                </div>
              </div>
              @else
              <div class="pricing-item">
                <div class="pricing-item-icon bg-danger text-white">
                  <i class="fas fa-times"></i>
                </div>
                <div class="pricing-item-label">
                  Pekerjaan belum selesai
                </div>
              </div>
              @endif
              
             
              
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12 col-lg-12 col-md-12 col-sm-12">
        <div class="card">
          <div class="card-body">
            <div class="summary">
              <div class="summary-item">
                <ul class="list-unstyled list-unstyled-border">

                  <li class="media">
                    <div class="media-body">
                      <div class="media-title">Pengirim Pesan Pengaduan</div>
                      <div class="text-small text-muted">
                        {{ $record->sender->name }}  
                      </div>
                    </div>
                  </li>

                  @if ($record->assigned != null)
                  <li class="media">
                    <div class="media-body">
                      <div class="media-title">Pelaksana Pekerjaan</div>
                      <div class="text-small text-muted">
                        {{ $record->executor->name }}  
                      </div>
                    </div>
                  </li>
                  @endif

                  <li class="media">
                    <div class="media-body">
                      <div class="media-title">Waktu Pengiriman Pesan</div>
                      <div class="text-small text-muted">
                        {{ \Carbon\Carbon::parse($record->created_at)->format('d M Y, H:i:s') }}  
                      </div>
                    </div>
                  </li>

                  @if ($record->assigned != null)
                    @if ($record->assigned->description != null)
                    <li class="media">
                      <div class="media-body">
                        <div class="media-title">Keterangan Hasil Pekerjaan</div>
                        <div class="text-small text-muted">
                          {{ $record->assigned->description }}  
                        </div>
                      </div>
                    </li>
                    @endif


                    @if ($record->assigned->start_work != null)
                    <li class="media">
                      <div class="media-body">
                        <div class="media-title">Waktu Memulai Pekerjaan</div>
                        <div class="text-small text-muted">
                          {{ \Carbon\Carbon::parse( $record->assigned->start_work)->format('d M Y, H:i:s') }}  
                        </div>
                      </div>
                    </li>
                    @endif

                    @if ($record->assigned->end_work != null)
                    <li class="media">
                      <div class="media-body">
                        <div class="media-title">Waktu Memulai Pekerjaan</div>
                        <div class="text-small text-muted">
                          {{ \Carbon\Carbon::parse( $record->assigned->start_work)->format('d M Y, H:i:s') }}  
                        </div>
                      </div>
                    </li>
                    @endif
                  @endif


                  @if ($record->assigned == null || $record->assigned->filepath == null)
                    <img alt="image" src="{{ asset('images/emptyimage.png') }}" width="250" height="250">
                  @else
                    <img alt="image" src='{{ url("").$record->assigned->filepath }}' width="400" height="300">
                  @endif

                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


@endsection

@push('scripts')
@endpush