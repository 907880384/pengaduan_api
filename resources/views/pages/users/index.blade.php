@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Master Pengguna</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item">
        <a href="{{ url('users/create') }}" class="btn btn-primary btn-sm">
          <i class="fas fa-plus"></i> TAMBAH PENGGUNA
        </a>
      </div>
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

            <div class="row">
              <div id="tableUser" class="col-md-12 col-12 col-lg-12">
                @include('pages.users.user_pagination')
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

    const urlDestroy = "{!! url('/users') !!}";
    const urlBackTo = "{!! url('/users') !!}"
    const urlPagination = "{!! url('/users') !!}"

    function parseQueryString() {
      var str = window.location.search;
      var objURL = {};
      
      str.replace(new RegExp( "([^?=&]+)(=([^&]*))?", "g" ), ( $0, $1, $2, $3 ) => {
        objURL[ $1 ] = $3;
      });
      return objURL;
    };

    function deleteRow(id) {
      const url = urlDestroy + "/" + id;
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
                  window.location.href = urlBackTo
                }
              })
            }
          }).catch((error) => {
            console.log(error.response);
          });
        }
      })
    }

    function filterDatatable(page) {
      const filterSearch = $("#filterSearch").val();
      const filterRole = $("#filterRole").val();

      $.ajax({
        type: "GET",
        url: urlPagination + '?page=' + page + '&filterRole=' + filterRole + '&filterSearch=' + filterSearch,
        success: function (data) {
          console.log("Render Data", data)
          $("#tableUser").html(data);
        }
      });
      console.log({page, filterSearch, filterRole});
    }

    function refreshDatatable() {
      $('select[name="filterRole"]').val('');
      $("#filterSearch").val('')
      filterDatatable(1)
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

        filterDatatable(page)

      });
    });

  </script>
@endpush