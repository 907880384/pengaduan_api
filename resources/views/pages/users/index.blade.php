@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Master Pengguna</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item">
        <a href="{{ url('users') }}">List Pengguna</a>
      </div>
      <div class="breadcrumb-item">
        <a href="{{ url('users/create') }}">Tambah Pengguna</a>
      </div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">List Pengguna</h2>
    <div class="row">
      <div class="col-lg-12 col-md-12 col-12">
        <div class="card">
          <div class="card-body p-1">
            
            <div class="table-responsive">
              <table class="table table-striped table-md">
                <thead>
                  <tr class="text-center">
                    <th>#</th>
                    <th>Nama</th>
                    <th>Unique ID/Username</th>
                    <th>Roles</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  
                  @foreach ($records as $key => $row)
                    <tr>
                      <td class="text-center">{{ $key + $records->firstItem() }}</td>
                      <td>{{ $row->name }}</td>
                      <td>{{ $row->username }}</td>
                      <td class="text-center">{{ $row->roles()->first()->name }}</td>
                      <td class="text-center">
                        <button type="button" class="btn btn-danger" onclick="deleteRow('{{ $row->id }}')">
                          <i class="fas fa-trash"></i> Delete
                        </button>
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

    const urlDestroy = "{!! url('/users') !!}";
    const urlBackTo = "{!! url('/users') !!}"

    function deleteRow(id) {
      const url = urlDestroy + "/" + id;
      Swal.fire({
        title: 'DELETE ROW',
        text: 'Anda yakin ingin menghapus data ini ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!'
      }).then((result) => {
        if (result.isConfirmed) {
          axios.delete(url).then((response) => {
            const {data, status} = response;
            if(status == 200) {
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
          }).catch((error) => {
            console.log(error.response);
          });
        }
      })
    }

  </script>
@endpush