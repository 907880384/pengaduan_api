@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Form Upload Barang</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item">
        <a href="{{ url('products') }}" class="btn btn-primary btn-sm">
          <i class="fas fa-clipboard-list"></i> DATA BARANG
        </a>
      </div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">Upload File Barang</h2>
    <div class="row">
      <div class="col-lg-12 col-md-12 col-12">
        <div class="card">
          <div class="card-body">
            
            <div class="row">
              <div class="col-lg-12 col-md-12">
                <form method="POST" enctype="multipart/form-data">
                  @csrf
                  <div class="form-group row">
                    <label for="productFile" class="col-sm-4 col-form-label">Upload File</label>
                    <div class="col-sm-8">
                      <input type="file" name="productFile" id="productFile" class="form-control form-control-sm">
                      <span class="badge badge-transparent">
                        Ekstensi file excel (.xls atau .xlxs)
                      </span>
                    </div>
                  </div>
  
                  <div class="form-group row">
                    <div class="col-sm-8 offset-sm-4">
                      <button id="btnUploadProduct" type="button" class="btn btn-info">
                        <i class="fas fa-save"></i> Save
                      </button>
                    </div>
                  </div>
                </form>
              </div>
  
  
              <div id="errorMessage" class="col-lg-12 col-md-12 alert-danger p-2" style="display: none">
                <h6 class="text-white">PESAN DATA BARANG YANG GAGAL DISIMPAN</h6>
                <div class="table-responsive">
                  <table id="errorProductInsertTable" class="table table-bordered table-hover nowrap table-sm" width="100%">
                    <thead>
                      <tr class="text-center bg-primary text-white">
                        <th>NAMA</th>
                        <th>SPESIFIKASI</th>
                        <th>STOK</th>
                        <th>SATUAN</th>
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
  const urlUpload = "{{ url('upload/file/products') }}";
  const urlUser = "{!! url('/products') !!}";

  $(function () {
    $('#btnUploadProduct').click(function (e) { 
      e.preventDefault();
      
      var fdata = new FormData();
      fdata.append('_token', '{{csrf_token()}}');
      fdata.append('productFile', document.getElementById("productFile").files[0]);

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

                if(data.errorData && data.errorData.length > 0) {
                  $("#errorMessage").css('display', '');
                  $table = $('#errorProductInsertTable').DataTable({
                    data: data.errorData,
                    columns: [
                      { title: "NAMA" },
                      { title: "SPESIFIKASI" },
                      { title: "STOK" },
                      { title: "SATUAN" },
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