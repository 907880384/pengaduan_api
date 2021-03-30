@extends('layouts.backend')

@section('baseStyles')
<link rel="stylesheet" href="{{ asset('css/carts.css') }}">
@endsection

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Keranjang Pemesanan</h1>
  </div>

  <div class="section-body">
    <h2 class="section-title">Data Pemesanan Barang</h2>
    <div class="row">
      <div class="col-12 col-lg-12 col-md-12">
        <div class="card">
          <div class="card-body p-1">
            
            <div class="row mt-2 mb-2">
              <div class="col-lg-12 col-md-12">
                <div class="card">
                  <div class="row">
                    
                    <div id="cartOrder" class="col-md-8 cart">
                      @include('pages.carts.carts')
                    </div>

                    <div class="col-md-4 summary">

                      <div>
                        <h5>
                          <b>Filter Pemesanan</b>
                        </h5>
                      </div>
                      <hr>
                      
                      <form>
                        <p>Nama Barang</p> 
                        <select id="selectProductOption">
                          <option value="-">Semuanya</option>
                          @foreach ($products as $product)
                            <option value="{{ $product->product_id }}">
                              {{ $product->product_name }}
                            </option>
                          @endforeach
                        </select>
                      </form>
                      
                      <button class="btn-checkout" onclick="filterCarts()">
                        FILTER
                      </button>
                    
                    </div>
                  </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>

const urlApi = "{!! url('/list/cart/orders') !!}"

function setDatatable(page) {
  const product_id = $('#selectProductOption').val();

  $.ajax({
    type: "GET",
    url: urlApi + '?page=' + page + '&product_id=' + product_id,
    success: function (data) {
      $("#cartOrder").html(data);
    }
  });
}

function filterCarts() {
  var hrefPage = $(this).attr('href');
  var page = 1;

  console.log(hrefPage);

  if(hrefPage != '#' && hrefPage != '' && hrefPage != undefined) {
    page = parseInt($(this).attr('href').split('page=')[1]);
  }
  
  setDatatable(page);
}


function setAgree(id) {
  console.log(id);
}

function setDisagree(id) {
  
}


$(function () {
  $(document).on('click', '.pagination a', function(event) {
    event.preventDefault();
    var hrefPage = $(this).attr('href');
    var page = 1;

    if(hrefPage != '#' && hrefPage != '' && hrefPage != undefined) {
      page = parseInt($(this).attr('href').split('page=')[1]);
    }

    setDatatable(page)

  });
});


</script>
@endpush