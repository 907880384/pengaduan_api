@extends('layouts.backend')

@section('baseStyles')
  <link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.1.2/css/rowGroup.dataTables.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/gijgo/1.9.13/combined/css/gijgo.min.css" integrity="sha512-oCuecFHHGu/Y4zKF8IoSoj5hQq1dLNIiUCwN08ChNW1VoMcjIIirAJT2JmKlYde6DeLN6JRSgntz6EDYDdFhCg==" crossorigin="anonymous" />
@endsection

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Informasi Pemesanan Barang</h1>
  </div>

  <div class="section-body">
    <h2 class="section-title">Pemesanan Barang</h2>
    <div class="row">
      <div class="col-12 col-lg-12 col-md-12">
        <div class="card">
          <div class="card-body p-1">
            
            <div class="row m-2">
              <div class="col-md-8">

                <div class="form-group row">
                  <label for="changeOrderDate" class="col-md-3 m-2">
                    Tanggal Pemesanan
                  </label>
                  <div class="col-md-8">
                    <input id="changeOrderDate"  class="form-control" />
                  </div>
                </div>

                <div class="form-group row">
                  <label for="selectStatusOrder" class="col-md-3 m-2">
                    Status Pemesanan
                  </label>
                  <div class="col-md-8">
                    <select id="selectStatusOrder" name="selectStatusOrder" class="form-control form-control-sm">
                      <option value="wait">Menunggu</option>
                      <option value="accept">Terima</option>
                      <option value="cancel">Tolak</option>
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <div class="col-md-8 offset-md-4 m-2">
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

            <div class="row">&nbsp;</div>
            

            <div class="row p-3">
              @include('pages.orders.datatable')
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</section>  

@include('pages.orders.model_disagree')
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdn.datatables.net/rowgroup/1.1.2/js/dataTables.rowGroup.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gijgo/1.9.13/combined/js/gijgo.min.js" integrity="sha512-T62eI76S3z2X8q+QaoTTn7FdKOVGjzKPjKNHw+vdAGQdcDMbxZUAKwRcGCPt0vtSbRuxNWr/BccUKYJo634ygQ==" crossorigin="anonymous"></script>

<script>
  
var thisTable = 'orderTable';
var urlApi = "{{ url('orders') }}";

function setDatatable(tableName , filter = null) {
  tableName = '#' + tableName;

  if($.fn.DataTable.isDataTable(tableName) ) {
    $(tableName).DataTable().destroy();
  }

  var table = $(tableName).DataTable({
    processing: true,
    serverSide: true,
    searching: false,
    destroy: true,
    ordering: false,
    ajax: {
      "url": "{{ route('list.orders') }}",
      "data": function (d) {
        if(filter == null) {
          d.orderDate = null;
          d.orderStatus = null;
        }
        else {            
          d.orderDate = filter.orderDate;
          d.orderStatus = filter.orderStatus
        }
      }   
    },
    columns: [
      { data: 'product_name', name: 'product_name' },
      {
        data: 'user', 
        name: 'user', 
        className: 'text-center',
        render: function(data) {
          return `<b>${data.name}</b>`;
        }
      },
      {data: 'quantity', name: 'quantity', className: 'text-center'},
      {
        data: 'product', 
        name: 'product', 
        className: 'text-center',
        render: function(data) {
          return data.satuan
        }
      },
      {
        data: 'order_date', 
        name: 'order_date',
        className: 'text-right',
        render: function(data) {
          if(data != '' && data != null && data != undefined)
            return moment(data).format('DD MMM YYYY, H:m:s')
          
          return ''
        }
      },
      {
        data: 'status', 
        name: 'status', 
        className: 'text-center',
        render: function(data, type, row) {
          if(row.is_agree) {
            return `<span class="badge badge-success">${data}</span>`
          }
          else {
            if(row.is_disagree) {
              return `<span class="badge badge-danger">${data}</span>`
            }
            else {
              return `<span class="badge badge-warning">${data}</span>`
            }
          }
        }
      },
      {
        data: 'reasons',
        name: 'reasons',
        render: function(data) {
          return data != null ? `<small>${data}</small>` : '';
        }
      },
      {data: 'action',name: 'action', className: 'text-center'}
    ],
    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    rowGroup: {
      startRender: null,
      endRender: function(rows, group) {
        console.log("Group", group)

        var sumQty = rows.data().pluck('quantity').reduce(function(a, b) {
          return a + b * 1;
        })
      
        return $('<tr/>')
          .append( '<td colspan="2">Total Pemesanan</td>' )
          .append( '<td class="text-center"><b>'+ sumQty +'</b></td>' )
          .append( '<td/>' )
          .append( '<td/>' )
          .append( '<td/>' )
          .append( '<td/>' )
          .append( '<td/>' )
      },
      dataSrc: 'product_name'
    }
  });

}

function setAgree(id) {
  const url = urlApi + "/agreed/" + id 
    
  Swal.fire({
    title: 'KONFIRMASI',
    text: 'Saya menyetujui permintaan pesanan barang ini',
    icon: 'info',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Ya!'
  }).then((result) => {
    if (result.isConfirmed) {
      axios.get(url).then((response) => {
        console.log("Response", response);
        
        const {data, status} = response;
        if(status == 200) {
          Swal.fire({
            title: 'SUCCESS',
            text: data.message,
            confirmButtonText: `OK`,
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = urlApi
            }
          })
        }
      }).catch((error) => {
        console.log(error.response);
      });
    }
  })
}

function setDisagree(id) {
  $("#modalDisagree").modal('show');
  $("#btnSaveReason").attr('data-id', id);
}

function refreshDatatable() {
  setDatatable(thisTable);
}

function filterDatatable() {
  const filter = {
    orderDate: $('#changeOrderDate').val(),
    orderStatus: $("#selectStatusOrder").val()
  }
  setDatatable(thisTable, filter);
}

$(function () {

  const timeElapsed = Date.now();
  const today = new Date(timeElapsed);

  $('#changeOrderDate').datepicker({
    uiLibrary: 'bootstrap4',
    value: today.toLocaleDateString(),
  });

  setDatatable(thisTable, {
    orderDate: $('#changeOrderDate').val(),
    orderStatus: $("#selectStatusOrder").val()
  });

  $("#btnSaveReason").click(function (e) { 
    e.preventDefault();
    const orders = {
      orderReason: $("#reasonCancel").val(),
      orderId: $(this).attr('data-id')
    };
    
    axios.post(urlApi + '/disagree', orders).then((response) => {
      const {data, status} = response;
      if(status == 200) {
        Swal.fire({
          title: 'KONFIRMASI',
          text: data.message,
          icon: 'success',
          confirmButtonText: `OK`,
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = urlApi
          }
        })
      }
    }).catch((err) => {
      const {data} = err.response;
      Swal.fire({
        title: 'ERROR',
        text: data.message,
        icon: 'error',
        confirmButtonText: `OK`,
      }).then((result) => {
        if (result.isConfirmed) {
        }
      })
    });
    
  });

});
</script>
@endpush