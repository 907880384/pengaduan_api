@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Info Tamu</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item">
        <a href="{{ url('/visitors') }}" class="btn btn-primary btn-sm">
          <i class="fas fa-arrow-circle-left"></i> KEMBALI
        </a>
      </div>
    </div>
  </div>

  <div class="section-body">

    <div class="row">
      <div class="col-12 col-lg-12 col-md-12 col-sm-12">
        <div class="card author-box card-primary">
          <div class="card-header">
            <h4>{{ $visitor->nama }}</h4>
          </div>

          <div class="card-body">
            <div class="row">
              <div class="col-md-4">
                @foreach ($visitor->fileVisitors as $files)
                  <img alt="image" src="{{ url($files->filepath) }}" class="img-fluid mb-1" />
                @endforeach
              </div>

              <div class="col-md-7 p-2">
                <h4>Detail Informasi</h4>
                <ul class="list-group list-group-flush">
                  <li class="list-group-item">
                    Nama Tamu 
                    <i class="fas fa-long-arrow-alt-right"></i>
                    {{ $visitor->nama }}
                  </li>

                  <li class="list-group-item">
                    No. Identitas ({{ $visitor->typeIdentity->name }}) 
                    <i class="fas fa-long-arrow-alt-right"></i>
                    {{ $visitor->no_identitas }}
                  </li>
                  
                  <li class="list-group-item">
                    No. HP  
                    <i class="fas fa-long-arrow-alt-right"></i>
                    {{ $visitor->no_hp }}
                  </li>

                  <li class="list-group-item">
                    Tujuan  
                    <i class="fas fa-long-arrow-alt-right"></i>
                    {{ $visitor->tujuan }}
                  </li>

                  @if ($visitor->keterangan != '')
                  <li class="list-group-item">
                    Keterangan  
                    <i class="fas fa-long-arrow-alt-right"></i>
                    {{ $visitor->keterangan }}
                  </li>
                  @endif
                  
                  <li class="list-group-item">
                    Status  
                    <i class="fas fa-long-arrow-alt-right"></i>
                    @if ($visitor->selesai)
                      <span class="badge badge-success">SELESAI</span>  
                    @else
                      <span class="badge badge-warning">BERTAMU</span>
                    @endif
                  </li>

                  <li class="list-group-item">
                    Waktu Bertamu  
                    <i class="fas fa-long-arrow-alt-right"></i>
                    {{ \Carbon\Carbon::parse($visitor->time_masuk)->format('d M Y, H:i:s') }}
                  </li>

                  @if ($visitor->selesai)
                  <li class="list-group-item">
                    Selesai Bertamu  
                    <i class="fas fa-long-arrow-alt-right"></i>
                    {{ \Carbon\Carbon::parse($visitor->time_keluar)->format('d M Y, H:i:s') }}
                  </li>
                  @else
                  <button type="button" class="btn btn-success" onclick="setDone('{{ $visitor->id }}')"><i class="fas fa-check"></i> 
                    SELESAI BERTAMU
                  </button>
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
<script>
  let urlApi = "{{ url('visitors') }}";

  function setDone(id) {
    axios.get(`${urlApi}/exit/${id}`).then((response) => {
      console.log(response);
      const {data, status} = response;
      if(status == 200) {
        Swal.fire({
          title: 'BERHASIL',
          text: data.message,
          icon: 'success',
          confirmButtonText: `OK`,
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = urlApi
          }
        });
      }
    }).catch((error) => {
      console.log(error.response)
      const {data, status} = response;
      Swal.fire({
        title: 'GAGAL',
        text: data.message,
        icon: 'error',
      });
    })
  }

</script>
@endpush