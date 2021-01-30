@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Informasi Pengaduan</h1>
    <div class="section-header-breadcrumb">
      @if (strtolower(Auth::user()->roles()->first()->slug) == 'pegawai')
      <div class="breadcrumb-item">
        <a href="{{ url('/complaints/create') }}">Tambah Pengaduan</a>
      </div>
      @endif
    </div>
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

                      <td class="text-center">{{ $row->complainer->name }}</td>
                      <td class="text-center">{{ $row->updated_at }}</td>

                      <td class="text-center">
                        @if ($row->finished)
                        <a href="#" class="badge badge-info">
                          <i class="fas fa-eye"></i> Detail
                        </a>
                        @endif

                        @if (auth()->user()->roles()->first()->slug === 'admin')
                          @if (!$row->on_assigned && !$row->finished)
                          <button class="badge badge-primary" onclick="showAssignModal('{{ $row->id }}', '{{ $row->typeComplaint->role_id }}')">
                            <i class="fas fa-tag"></i> Assign
                          </button>
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

<div id="modalAssign" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ModalAssignComplaint" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Assign Pengaduan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12 col-12">
            <form>
              <div class="form-group row">
                <label for="selectAssign" class="col-form-label col-md-4">
                  Assigned To
                </label>
                <div class="col-md-8">
                  <select class="form-control" id="selectAssign"></select>
                </div>
              </div>
              <div class="form-group row">
                <div class="offset-md-4 col-md-8">
                  <button id="buttonAssign" class="btn btn-primary" data-id="">
                    Apply & Assign
                  </button>
                </div>
              </div>
            </form>     
          </div>
        </div>   
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          Close
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
  <script>
    const urlBackTo = "{{ url('/complaints') }}"
    const urlUsers = "{{ url('/users/roles') }}";
    const urlAssigned = "{{ url('/assigned/complaints') }}";

    function setSelect2(data, select, option = null) {
      let records = [];
      $.each(data, function (i, v) {
        records.push({id: v.id, text: v.name + ' - ' + v.roles[0].name});
      });

      select.empty();
      select.append(new Option());
      
      select.select2({
        dropdownParent: $('#modalAssign'),
        data: records,
        placeholder: "User Operational",
        allowClear: true,
      });

      if(option != null) {
        select.val(option).trigger('change');
      }
    }

    function showAssignModal(id, role_id) {
      console.log("ID", id);
      console.log("ROLE ID", role_id);
      $("#modalAssign").modal('show');
      $("#buttonAssign").attr('data-id', id);

      axios.get(urlUsers + '/' + role_id).then((response) => {
        console.log(response);
        if(response.status === 200) {
          const {users} = response.data;
          setSelect2(users, $("#selectAssign"), null);
        }
      }).catch((err) => {
        console.log(err.response);
      });
    }

    $(function () {

      $("#buttonAssign").click(function (e) { 
        e.preventDefault();
        const data = {
          id: $(this).attr('data-id'),
          user_perform_id: $("#selectAssign").val()
        }
        console.log(data);
        

        axios.post(urlAssigned, data).then((response) => {
          console.log(response);
          const {data, status} = response;
          if(status === 200) {
            Swal.fire({
              title: 'SUCCESS',
              text: data.message,
              confirmButtonText: `OK`,
            }).then((result) => {
              if (result.isConfirmed) {
                $("#modalAssign").modal('hide');
                window.location.href = urlBackTo
              }
            })
          }
        }).catch((err) => {
          console.log(err.response);
        })
      });

    });
  </script>
@endpush