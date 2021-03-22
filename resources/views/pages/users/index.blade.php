@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Master Pengguna</h1>
    <div class="section-header-breadcrumb">
      @if (auth()->user()->roles()->first()->slug == 'developer')
      <div class="breadcrumb-item">
        <a href="{{ url('users/view/upload') }}" class="btn btn-primary btn-sm">
          <i class="fas fa-file-import"></i> IMPORT FILE USER
        </a>
      </div>
      @endif

      @if (auth()->user()->roles()->first()->slug == 'admin')
      <div class="breadcrumb-item">
        <a href="{{ url('users/create') }}" class="btn btn-primary btn-sm">
          <i class="fas fa-plus"></i> TAMBAH PENGGUNA
        </a>
      </div>
      @endif
      
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">List Pengguna</h2>
    <div class="row">
      <div class="col-lg-12 col-md-12 col-12">
        <div class="card">
          <div class="card-body p-1">
            <div class="row m-2">
              <div class="col-md-6">
                <div class="form-group row">
                  <label for="filterRole" class="col-md-3 m-2">Kategori Role</label>
                  <div class="col-md-8">
                    <select id="filterRole" name="filterRole" class="form-control-sm form-control">
                      <option value="">Pilih Role Akses</option>
                      @foreach ($roles as $role)
                        <option value="{{ $role->id }}">
                          {{ $role->fullslug }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group row">
                  <label for="filterSearch" class="col-md-3 m-2">Pencarian</label>
                  <div class="col-md-8">
                    <input type="text" id="filterSearch" class="form-control" />
                  </div>
                </div>
              </div>

            </div>

            <div class="row m-2">
              <div class="col-md-12">
                <button type="button" class="btn btn-primary btn-sm" onclick="filterDatatable()">
                  <i class="fa fa-filter"></i> Filter
                </button>

                <button type="button" class="btn btn-success btn-sm" onclick="refreshDatatable()">
                  <i class="fa fa-spinner"></i> Refresh
                </button>
              </div>
            </div>

            <div class="row">&nbsp;</div>

            <div class="row p-3">
              @include('pages.users.user_pagination')
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
  var userTable = 'userTable';

  const urlUser = "{!! url('/users') !!}";

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
        "url": "{{ route('list.users') }}",
        "data": function (d) {
          if(filter != null) {
            d.filterRole = filter.filterRole;
            d.filterSearch = filter.filterSearch;
          }          
        }   
      },
      columns: [
        {data: 'DT_RowIndex',  name: 'DT_RowIndex', className: 'text-center'},
        {data: 'name', name: 'name'},
        {data: 'username', name: 'username', className:'text-center', render: function(data) { return `<b>${data}</b>`}},
        {data: 'role_name', name: 'role_name'},
        {data: 'action',name: 'action', className:'text-center'},
      ],
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
    });

  }

  function filterDatatable() {
    const filter = {
      filterRole: $("#filterRole").val(),
      filterSearch: $("#filterSearch").val()
    }

    console.log(filter);

    setDatatable(userTable, filter)
  }

  function refreshDatatable() {
    $('select[name="filterRole"]').val('');
    $("#filterSearch").val('')
    setDatatable(userTable)
  }

  function parseQueryString() {
    var str = window.location.search;
    var objURL = {};
    
    str.replace(new RegExp( "([^?=&]+)(=([^&]*))?", "g" ), ( $0, $1, $2, $3 ) => {
      objURL[ $1 ] = $3;
    });
    return objURL;
  };

  function deleteRow(id) {
    const url = urlUser + "/" + id;
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
                window.location.href = urlUser
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
    setDatatable(userTable);
  });

</script>
@endpush