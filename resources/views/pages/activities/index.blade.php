@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Aktivitas Pengaduan</h1>
  </div>


  <div class="section-body">
    <h2 class="section-title">List Data Pengaduan</h2>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body p-1">
            
            <div class="table-responsive">
              <table id="complaintTable" class="table table-condensed" style="width: 100%">
                <thead>
                  <tr class="text-center">
                    <th>#</th>
                    <th>Perihal/Judul</th>
                    <th>Pesan Pengaduan</th>
                    <th>Penting (Urgent)</th>
                    <th>Selesai (Finish)</th>
                    <th>Penugasan (Assigned)</th>
                    <th>Pengirim Pesan</th>
                    <th>Pelaksana</th>
                    <th>Waktu Pengiriman</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  
                  @foreach ($records as $key => $row)
                    <tr>
                      <td class="text-center">{{ $key + $records->firstItem() }}</td>
                      <td class="text-center">{{ $row->title }}</td>
                      <td>{{ $row->messages }}</td>
                      {{-- Urgent --}}
                      <td class="text-center">
                        @if ($row->is_urgent)
                          <div class="badge badge-danger">
                            PENTING
                          </div>
                        @else 
                          <div class="badge badge-info">
                            BIASA
                          </div>
                        @endif
                      </td>
                      {{-- Selesai --}}
                      <td class="text-center">
                        @if ($row->is_finished)
                          <div class="badge badge-primary">
                          SELESAI
                          </div> 
                        @else
                        <div class="badge badge-secondary">
                          BELUM SELESAI
                        </div>
                        @endif
                      </td>
                      {{-- Penugasan --}}
                      <td class="text-center">
                        @if ($row->is_assigned)
                          <div class="badge badge-success">
                            DITUGASKAN
                          </div>
                        @else
                          <div class="badge badge-warning">
                            MENUNGGU DITUGASKAN
                          </div>
                        @endif
                      </td>
                      <td class="text-center">{{ $row->sender->name }}</td>
                      <td class="text-center">
                        @if ($row->executor)
                          <b>{{ $row->executor->name }}</b>
                        @else
                          &nbsp;
                        @endif
                      </td>
                      <td class="text-center">{{ $row->created_at }}</td>

                      <td class="text-center">
                        @if ($row->is_finished)
                        <a href="#" class="badge badge-info">
                          <i class="fas fa-eye"></i> Detail
                        </a>
                        @endif

                        @if (auth()->user()->roles()->first()->slug != 'admin' && auth()->user()->roles()->first()->slug != 'pegawai' )
                          @if (!$row->assigned->is_accepted && !$row->is_finished)
                          <button class="badge badge-primary" onclick="startWork('{{ $row->assigned->id }}')">
                            <i class="fas fa-tag"></i> Start Work
                          </button>
                          @endif

                          @if ($row->assigned->is_accepted && !$row->is_finished)
                          <a href="{{ url('show/finished/working/complaint/'. $row->id) }}" class="badge badge-success">
                            <i class="fas fa-tag"></i> Finish Work
                          </a>
                          @endif
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
	            
            </div>
            
          </div>

          {{ $records->links('customs.pagination') }}

        </div>
      </div>

    </div>
  </div>
</section>
@endsection

@push('scripts')
  <script>
    const urlStart = "{{ url('accept/assigned') }}";
    const urlBackTo = "{{ url('activities') }}";

    function startWork(assignId) {
      axios.get(urlStart + '/' + assignId + '/complaints').then((response) => {
        console.log(response);
        const {data, status} = response;
        if(status === 200) {
          Swal.fire({
            title: 'SUCCESS',
            text: data.message,
            confirmButtonText: `OK`,
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = urlBackTo
            }
          })
        }
      }).catch((err) => {
        console.log(err.response);
      })

    }

    function finishWork(row) {
      console.log("Finished Work", row)
    }
  </script>
@endpush