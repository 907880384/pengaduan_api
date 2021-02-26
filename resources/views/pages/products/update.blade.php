@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Form Update Barang</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item">
        <a href="{{ url('products') }}">List Barang</a>
      </div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">Ubah Data Barang {{ $product->id }}</h2>
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-7">
            <form enctype="multipart/form-data" method="post">
              @csrf
              
              <!-- Name -->
              <div class="form-group row">
                <label for="productName" class="col-sm-4 col-form-label">
                  Nama Barang
                </label>
                <div class="col-sm-8">
                  <input type="text" id="productName" class="form-control" value="{{ $product->product_name }}" />
                </div>
              </div>
  
              <!-- Spesification -->
              <div class="form-group row">
                <label for="productSpesification" class="col-sm-4 col-form-label">
                  Spesifikasi
                </label>
                <div class="col-sm-8">
                  <textarea id="productSpesification" class="form-control" rows="10" cols="30">{{ $product->spesification }}</textarea>
                </div>
              </div>
  
              <!-- Stock -->
              <div class="form-group row">
                <label for="productStock" class="col-sm-4 col-form-label">
                  Stok
                </label>
                <div class="col-sm-8">
                  <input type="text" id="productStock" class="form-control" value="{{ $product->stock_awal }}" />
                </div>
              </div>
  
              <!-- Satuan -->
              <div class="form-group row">
                <label for="productSatuan" class="col-sm-4 col-form-label">
                  Satuan
                </label>
                <div class="col-sm-8">
                  <input type="text" id="productSatuan" class="form-control" value="{{ $product->satuan }}" />
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
                  <button type="button" class="btn btn-primary" onclick="updateData('{{ $product->id }}')">
                    <i class="fas fa-save"></i> Update
                  </button>
                </div>
              </div>
            </form>
          </div>
  
          @if (count($product->fileImages) > 0)
          <div class="col-md-5">
            <div class="card card-info">
              <div class="card-header">
                <h4>Gambar {{ $product->product_name }}</h4>
              </div>
              <div class="card-body">
                <div class="gallery gallery-fw" data-item-height="200">
                  @foreach ($product->fileImages as $item)
                  <div class="gallery-item" data-image="{{ asset('storage/'. $item->filepath) }}" data-title="{{ $item->filename }}"></div>
                  @endforeach
                </div>
              </div>
  
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
  <script>

    const urlProducts = "{{ url('products') }}";

    function updateData(id) {
      console.log("Product ID", id);

      var fdata = new FormData();

      fdata.append('_token', '{{csrf_token()}}');
      fdata.append("product_name", $("#productName").val());
      fdata.append("spesification", $("#productSpesification").val());
      fdata.append("stock", $("#productStock").val());
      fdata.append("satuan", $("#productSatuan").val());

      const totalFiles = document.getElementById("productImage").files.length;
      
      if(totalFiles > 0) {
        for (let i = 0; i < totalFiles; i++) {
          const element = document.getElementById("productImage").files[i];
          console.log(`Element ${i}`, element);
          fdata.append("fileImages[]", element);
        }
      }

      axios.post(`${urlProducts}/${id}`, fdata).then((response) => {
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