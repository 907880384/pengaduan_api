@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Form Tambah Barang</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item">
        <a href="{{ url('products') }}">List Barang</a>
      </div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">Tambah Barang Baru</h2>
    <div class="card">
      <div class="card-body p-5">
        
        <div class="col-md-7">
          <form enctype="multipart/form-data" method="post">
            @csrf
            
            <!-- Name -->
            <div class="form-group row">
              <label for="productName" class="col-sm-4 col-form-label">
                Nama Barang
              </label>
              <div class="col-sm-8">
                <input type="text" id="productName" class="form-control" />
              </div>
            </div>

            <!-- Spesification -->
            <div class="form-group row">
              <label for="productSpesification" class="col-sm-4 col-form-label">
                Spesifikasi
              </label>
              <div class="col-sm-8">
                <textarea id="productSpesification" class="form-control" rows="10" cols="30"></textarea>
              </div>
            </div>

            <!-- Stock -->
            <div class="form-group row">
              <label for="productStock" class="col-sm-4 col-form-label">
                Stok
              </label>
              <div class="col-sm-8">
                <input type="text" id="productStock" class="form-control" />
              </div>
            </div>

            <!-- Satuan -->
            <div class="form-group row">
              <label for="productSatuan" class="col-sm-4 col-form-label">
                Satuan
              </label>
              <div class="col-sm-8">
                <input type="text" id="productSatuan" class="form-control" />
              </div>
            </div>

            <!-- Image -->
            <div class="form-group row">
              <label for="productImage" class="col-sm-4 col-form-label">
                Upload Gambar
              </label>
              <div class="col-sm-8">
                <input type="file" name="productImage[]" id="productImage" class="form-control" multiple>
              </div>
            </div>

            <!-- Button -->
            <div class="form-group row">
              <div class="col-sm-8 offset-sm-4">
                <button type="button" class="btn btn-primary" onclick="saveData()">
                  <i class="fas fa-save"></i> Simpan
                </button>
              </div>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
  <script>

    const urlProducts = "{{ url('products') }}";

    function saveData() {
      var fdata = new FormData();

      fdata.append('_token', '{{csrf_token()}}');
      fdata.append("product_name", $("#productName").val());
      fdata.append("spesification", $("#productSpesification").val());
      fdata.append("stock", $("#productStock").val());
      fdata.append("satuan", $("#productSatuan").val());

      const totalFiles = document.getElementById("productImage").files.length;

      console.log("Total Files", totalFiles)

      if(totalFiles > 0) {
        for (let i = 0; i < totalFiles; i++) {
          const element = document.getElementById("productImage").files[i];
          console.log(`Element ${i}`, element);
          fdata.append("fileImages[]", element);
        }
      }

      console.log(fdata.get('fileImages'))

      axios.post(urlProducts, fdata).then((response) => {
        console.log(response);    
        const {data, status} = response;

        if(status === 200) {
          Swal.fire({
            title: 'SUCCESS',
            text: data.message,
            confirmButtonText: `OK`,
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = urlProducts
            }
          });
        }

      }).catch((err) => {
        console.log(err.response);
      });

    }
  </script>
@endpush