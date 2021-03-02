@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Informasi Pemesanan dan Pengambilan Barang</h1>
  </div>

  <div class="section-body">
    <h2 class="section-title">List Pemesanan dan Pengambilan Barang</h2>
    <div class="row">
      <div class="col-12 col-lg-12 col-md-12">
        <div class="card">
          <div class="card-body p-1">
            
            <div class="row">
              <div id="coverTableOrder" class="col-md-12 col-12 col-lg-12">
                @include('pages.orders.datatable')
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

  const urlOrder = "{!! url('/orders') !!}"

  function setDatatable(page) { 
    $.ajax({
      type: "GET",
      url: urlOrder + '?page=' + page,
      success: function (data) {
        $("#coverTableOrder").html(data);
      }
    });
  }

  function filterDatatable() {
    var hrefPage = $(this).attr('href');
    var page = 1;

    if(hrefPage != '#' && hrefPage != '' && hrefPage != undefined) {
      page = parseInt($(this).attr('href').split('page=')[1]);
    }

    setDatatable(page);
  }

  function refreshDatatable() {
    setDatatable(1);
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