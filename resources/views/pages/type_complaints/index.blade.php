@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Master Kategori Pengaduan</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item">
        <a href="#">Kategori List</a>
      </div>
      <div class="breadcrumb-item">
        <a href="#">Tambah Kategori</a>
      </div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">List Kategori Pengaduan</h2>
    <div class="row">
      <div class="col-lg-12 col-md-12 col-12">
        <div class="card">
          <div class="card-body p-1">
            
            <div class="table-responsive">
              <table class="table table-striped table-md">
                <thead>
                  <tr class="text-center">
                    <th>#</th>
                    <th>Judul/Kategori</th>
                    <th>Sub Operational</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  
                  @foreach ($records as $key => $row)
                    <tr>
                      <td class="text-center">{{ $key + $records->firstItem() }}</td>
                      <td>{{ $row->title }}</td>
                      <td class="text-center">{{ $row->roleType->name }}</td>
                      <td>
                        <a href="{{ url('categories/complaint/'.$row->id.'/edit') }}" class="btn btn-primary">
                          <i class="fas fa-edit"></i> Edit
                        </a>

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

    function deleteRow(id) {
      const url = "{!! url('/categories/complaint') !!}/" + id;
      Swal.fire({
        title: 'DELETE ROW',
        text: 'Anda yakin ingin menghapus data ini ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Hapus!'
      }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
          axios.delete(url).then((response) => {

            console.log(response);
            const {data, status} = response;
            if(status == 200) {
              Swal.fire({
                title: 'SUCCESS',
                text: data.message,
                confirmButtonText: `OK`,
              }).then((result) => {
                if (result.isConfirmed) {
                  window.location.href = "{!! url('/categories/complaint') !!}"
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