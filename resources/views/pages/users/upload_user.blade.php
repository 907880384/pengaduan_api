@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Form Upload Pengguna</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item">
        <a href="{{ url('users') }}" class="btn btn-primary btn-sm">
          <i class="fas fa-clipboard-list"></i> LIST PENGGUNA
        </a>
      </div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">Upload File Pengguna</h2>
    <div class="row">
      <div class="col-lg-12 col-md-12 col-12">
        <div class="card">
          <div class="card-body">
            
            <div class="row">
              <div class="col-lg-5 col-md-5">
                <form method="POST" enctype="multipart/form-data">
                  @csrf
                  <div class="form-group row">
                    <label for="userFile" class="col-sm-4 col-form-label">Upload File</label>
                    <div class="col-sm-8">
                      <input type="file" name="userFile" id="userFile" class="form-control form-control-sm">
                      <span class="badge badge-transparent">
                        Ekstensi file excel (.xls atau .xlxs)
                      </span>
                    </div>
                  </div>
  
                  <div class="form-group row">
                    <div class="col-sm-8 offset-sm-4">
                      <button id="btnUploadUser" type="button" class="btn btn-info">
                        <i class="fas fa-save"></i> Save
                      </button>
                    </div>
                  </div>
                </form>
              </div>
  
  
              <div class="col-lg-7 col-md-7 alert-danger p-2">
                <h6 class="text-white">PESAN DATA PENGGUNA YANG GAGAL DISIMPAN</h6>
                <div class="table-responsive">
                  <table id="errorUserInsertTable" class="table table-bordered table-hover nowrap table-sm" width="100%">
                    <thead>
                      <tr class="text-center bg-primary text-white">
                        <th>NAMA</th>
                        <th>USERNAME</th>
                        <th>PASSWORD</th>
                        <th>ROLES</th>
                      </tr>
                    </thead>
                  </table>
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

@push('scripts')
<script>
  const urlUpload = "{{ url('upload/file/users') }}";
  const urlUser = "{!! url('/users') !!}";

  $(function () {
    $('#btnUploadUser').click(function (e) { 
      e.preventDefault();
      
      var fdata = new FormData();
      fdata.append('_token', '{{csrf_token()}}');
      fdata.append('userfile', document.getElementById("userFile").files[0]);

      axios.post(urlUpload, fdata).then((response) => {
        console.log('Success Response', response);
        
        const {data, status} = response;

        if(status == 200) {
          Swal.fire({
            title: data.success == false ? 'PERINGATAN' : 'BERHASIL',
            text: data.message,
            icon: data.success == false ? 'warning' : 'success',
            confirmButtonText: `OK`,
          }).then((result) => {
            if (result.isConfirmed) {
              if(data.success == false) {

                if(data.errors && data.errors.length > 0) {
                  $table = $('#errorUserInsertTable').DataTable({
                    data: data.errors,
                    columns: [
                      { title: "Name" },
                      { title: "USERNAME" },
                      { title: "PASSWORD" },
                      { title: "ROLES" },
                    ]
                  })
                }

              }
            }
          });
        }
      
      }).catch((error) => {
        console.log('Error Response', error.response);
        const {data} = error.response;

        Swal.fire({
          title: 'ERROR',
          text: data.message,
          icon: 'error',
          confirmButtonText: `CLOSE`,
        }).then((result) => {
          if (result.isConfirmed) {
            
          }
        });
      });

    });


  });

</script>
@endpush