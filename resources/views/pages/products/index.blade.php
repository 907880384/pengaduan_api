@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Informasi Data Barang</h1>
    <div class="section-header-breadcrumb">
      @if (strtolower(Auth::user()->roles()->first()->slug) == 'developer')
      <div class="breadcrumb-item">
        <a href="{{ url('/products/create') }}" class="btn btn-primary btn-sm">
          <i class="fas fa-plus"></i> TAMBAH BARANG
        </a>

        <a href="{{ url('products/view/upload') }}" class="btn btn-primary btn-sm">
          <i class="fas fa-file-import"></i> IMPORT FILE BARANG
        </a>
      </div>

      @endif
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">List Data Barang</h2>

    <div class="row">
      <div class="col-12 col-lg-12 col-md-12">
        <div class="card">
          <div class="card-heder ml-3 mt-3">
            <h5 class="card-title">Filter Pencarian</h5>
          </div>
          <div class="card-body p-1">
            <div class="row m-2">
              <div class="col-md-6">  
                <div class="form-group row">
                  <label for="searchName" class="col-md-4 m-2">Nama Barang</label>
                  <div class="col-md-7">
                    <input type="text" id="searchName" class="form-control" /> 
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group row">
                  <label for="searchSpesification" class="col-md-4 m-2">Spesifikasi</label>
                  <div class="col-md-7">
                    <input type="text" id="searchSpesification" class="form-control" /> 
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group row">
                  <div class="col-md-7 offset-md-5 m-2">
                    <button type="button" class="btn btn-primary btn-sm" onclick="filterDatatable()">
                      <i class="fa fa-filter"></i> Filter
                    </button>

                    <button type="button" class="btn btn-success btn-sm" onclick="refreshDatatable()">
                      <i class="fa fa-spinner"></i> Refresh
                    </button>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12 col-sm-12 col-md-12 ">
        <div class="card">
          <div id="coverTableProduct" class="card-body">
            @include('pages.products.datatable')
          </div>
        </div>
      </div>
    </div>

  </div>
</section>  
@endsection

@push('scripts')
<script>

  const urlProduct = "{!! url('/products') !!}"

  function setDatatable(page) {
    const searchName = $('#searchName').val();
    const searchSpesification = $('#searchSpesification').val();

    $.ajax({
      type: "GET",
      url: urlProduct + '?page=' + page + '&name=' + searchName + '&spesification=' + searchSpesification,
      success: function (data) {
        $("#coverTableProduct").html(data);
      }
    });
  }

  function filterDatatable() {
    var hrefPage = $(this).attr('href');
    var page = 1;

    console.log(hrefPage);

    if(hrefPage != '#' && hrefPage != '' && hrefPage != undefined) {
      page = parseInt($(this).attr('href').split('page=')[1]);
    }
    
    setDatatable(page);
  }

  function refreshDatatable() {
    $('#searchName').val('')
    $('#searchSpesification').val('')
    setDatatable(1);
  }

  function deleteRow(id) {
    const url = urlProduct + "/" + id;

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
                  window.location.href = urlProduct
                }
              })
            }


          }).catch((error) => {
            console.log(error.response);
          });
        }
      })
  }


  $(function () {
    $(document).on('click', '.pagination a',function(event)
      {
        event.preventDefault();
        var hrefPage = $(this).attr('href');
        var page = 1;

        if(hrefPage != '#' && hrefPage != '') {
          page = parseInt($(this).attr('href').split('page=')[1]);
        }

        setDatatable(page)

      });
  });

</script>
@endpush