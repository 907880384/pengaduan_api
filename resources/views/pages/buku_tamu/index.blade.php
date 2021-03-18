@extends('layouts.backend')




@section('content')
<section class="section">
  <div class="section-header">
    <h1>BUKU TAMU</h1>
    <div class="section-header-breadcrumb">
      @if (auth()->user()->roles()->first()->slug != 'admin')
      <div class="breadcrumb-item">
        <a href="{{ url('visitors/create') }}" class="btn btn-primary btn-sm">
          <i class="fas fa-plus"></i> TAMBAH TAMU
        </a>
      </div>
      @endif
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">List Data Tamu</h2>
    
    <div class="row">
      <div class="col-12 col-lg-12 col-md-12">
        <div class="card">
          <div class="card-body p-1">

            <div class="row m-2">
              <div class="col-md-6">
                <div class="form-group row">
                  <label for="filterStatus" class="col-md-3 m-2">Status Tamu</label>
                  <div class="col-md-8">
                    <select id="filterStatus" name="filterStatus" class="form-control-sm form-control">
                      <option value="bertamu">Bertamu</option>
                      <option value="selesai">Selesai</option>
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="filterTimeIn" class="col-md-3 m-2">Waktu Masuk</label>
                  <div class="col-md-8">
                    <input type="date" id="filterTimeIn" name="filterTimeIn" class="form-control"  />
                  </div>
                </div>

                <div class="form-group row">
                  <div class="col-md-8 offset-md-3 m-2">
                    <button type="button" class="btn btn-primary" onclick="filterVisitor()">
                      <i class="fas fa-filter"></i> Filter
                    </button>

                    <button type="button" class="btn btn-success" onclick="refreshVisitor()">
                      <i class="fa fa-spinner"></i> Refresh
                    </button>
                  </div>
                </div>
              </div>

              

            </div>


            <div class="row">&nbsp;</div>
            
            <div id="tableVisitors" class="row p-3">
              @include('pages.buku_tamu.visitor')
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
  let urlApi = "{{ url('visitors') }}"

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
        "url": "{{ route('list.visitors') }}",
        "data": function (d) {
          console.log("Filter", filter);

          if(filter == null) {
            d.isDone = false;
            d.timeIn = null;
          }
          else {            
            d.isDone = filter.isDone;
            d.timeIn = filter.timeIn;
          }
        }   
      },
      columns: [
        {data: 'DT_RowIndex',  name: 'DT_RowIndex', className: 'text-center'},
        {data: 'nama', name: 'nama'},
        {
          data: 'no_identitas', 
          name: 'no_identitas', 
          render: function(data, type, row) {
            console.log(row)
            return `No. ${row.type_identity.name} <i class="fas fa-arrow-alt-circle-right"></i> ${row.no_identitas}`;
          }
        },
        {
          data: 'selesai', 
          name: 'selesai', 
          render: function(data) {
            if(data) {
              return '<span class="badge badge-success">SELESAI</span>'
            }
            else {
              return '<span class="badge badge-warning">BERTAMU</span>'
            }
          }
        },
        {
          data: 'time_masuk', 
          name: 'time_masuk',
          render: function(data, type, row) {
            if(data != '' && data != null && data != undefined)
              return moment(data).format('DD MMM YYYY, H:m:s')
            
            return ''
          }
        },
        {
          data: 'time_keluar', 
          name: 'time_keluar',
          render: function(data, type, row) {
            if(data != '' && data != null && data != undefined)
              return moment(data).format('DD MMMM YYY, H:m:s')
            
            return ''
          }
        },
        {data: 'no_hp', name: 'no_hp'},
        {data: 'tujuan', name: 'tujuan'},
        {data: 'keterangan', name: 'keterangan'},
        {data: 'action',name: 'action'},
      ],
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
    });

  }

  function setDone(id) {
    axios.get(`${urlApi}/exit/${id}`).then((response) => {
      const {data, status} = response;
      if(status == 200) {
        Swal.fire({
          title: 'BERHASIL',
          text: data.message,
          icon: 'success',
          confirmButtonText: `OK`,
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = urlApi
          }
        });
      }
    }).catch((error) => {
      const {data, status} = response;
      Swal.fire({
        title: 'GAGAL',
        text: data.message,
        icon: 'error',
      });
    })
  }

  function filterVisitor() {
    
    setDatatable('visitorTable', {
      isDone: $("#filterStatus").val() == 'bertamu' ? false : true ,
      timeIn: $("#filterTimeIn").val()
    })
  }


  function refreshVisitor() {
    setDatatable('visitorTable');
    $("#filterStatus").val('bertamu')
    $("#filterTimeIn").val('');
  }

  function deleteRow(id) {
    const url = urlApi + "/" + id 
    
    Swal.fire({
        title: 'HAPUS DATA TAMU',
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
  

  $(function () {
    setDatatable('visitorTable');
  });


</script>
@endpush