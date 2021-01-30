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
                    <th>Kategori</th>
                    <th>Pesan</th>
                    <th>Mode</th>
                    <th>Status</th>
                    <th>Assign</th>

                    <th>Work Status</th>
                    <th>Pengirim Pesan</th>
                    <th>Waktu Pengiriman</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  
                  @foreach ($records as $key => $row)
                    <tr>
                      <td class="text-center">{{ $key + $records->firstItem() }}</td>
                      <td class="text-center">{{ $row->typeComplaint->title }}</td>
                      <td>{{ $row->messages }}</td>
                      <td class="text-center">
                        @if ($row->urgent)
                          <div class="badge badge-danger">
                            <i class="fas fa-bomb"></i> urgent
                          </div>
                        @else 
                          <div class="badge badge-info">
                            <i class="fas fa-circle"></i> normal
                          </div>
                        @endif
                      </td>

                      <td class="text-center">
                        @if ($row->finished)
                          <div class="badge badge-success">
                            <i class="fas fa-check-circle"></i> finish
                          </div>
                          
                        @else
                        <div class="badge badge-secondary">
                          <i class="fas fa-pause-circle"></i> on progress
                        </div>
                        @endif
                      </td>

                      <td class="text-center">
                        @if ($row->finished)
                          <div class="badge badge-success">
                            Assigned and Finished
                          </div>
                        @else
                          @if ($row->on_assigned)
                            <div class="badge badge-primary">
                              <i class="fas fa-handshake"></i> assigned
                            </div>
                          @else
                          <div class="badge badge-warning">
                            <i class="fas fa-power-off"></i> not assigned
                          </div>
                          @endif
                        @endif
                      </td>

                      <td class="text-center">
                        @if ($row->assigned->is_working)
                          <div class="badge badge-default">
                            Started Work
                          </div>
                        @else
                          <div class="badge badge-warning">
                            Waiting To Start
                          </div>
                        @endif
                      </td>

                      <td class="text-center">{{ $row->complainer->name }}</td>
                      <td class="text-center">{{ $row->updated_at }}</td>

                      <td class="text-center">
                        @if ($row->finished)
                        <a href="#" class="badge badge-info">
                          <i class="fas fa-eye"></i> Detail
                        </a>
                        @endif

                        @if (auth()->user()->roles()->first()->slug != 'admin' && auth()->user()->roles()->first()->slug != 'pegawai' )
                          @if (!$row->assigned->is_working && !$row->finished)
                          <button class="badge badge-primary" onclick="startWork('{{ $row }}')">
                            <i class="fas fa-tag"></i> Start Work
                          </button>
                          @endif

                          @if ($row->assigned->is_working && !$row->finished)
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
    const urlStart = "{{ url('start/working/complaint/') }}";
    const urlBackTo = "{{ url('activities') }}";

    function startWork(row) {
      row = JSON.parse(row);
      console.log("Started Work", row);
      axios.get(urlStart + "/" + row.assigned.id).then((response) => {
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