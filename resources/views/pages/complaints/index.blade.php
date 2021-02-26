@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Informasi Pengaduan</h1>
    <div class="section-header-breadcrumb">
      @if (strtolower(Auth::user()->roles()->first()->slug) == 'pegawai')
      <div class="breadcrumb-item">
        <a href="{{ url('/complaints/create') }}">Tambah Pengaduan</a>
      </div>
      @endif
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">List Data Pengaduan</h2>
    <div class="row">
      <div class="col-12 col-lg-12 col-md-12">
        <div class="card">
          <div class="card-body p-1">
            
            <div class="row m-2">
              <div class="col-md-6">
                <div class="form-group row">
                  <label for="sentences" class="col-md-3 m-2">Aktivitas</label>
                  <div class="col-md-8">
                    <select id="sentences" name="sentences" class="form-control-sm form-control">
                      @foreach ($activities as $item)
                        <option value="{{ $item['type'] }}">{{ Str::ucfirst($item['value']) }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group row">
                  <label for="search" class="col-md-3 m-1">Pencarian</label>
                  <div class="col-md-5 m-0">
                    <input type="text" id="search" class="form-control" /> 
                  </div>
                  <div class="col-md-3">
                    <button type="button" class="btn btn-secondary">
                      <i class="fa fa-search"></i> Cari
                    </button>
                  </div>
                </div>
              </div>

            </div>

            <div class="row m-2">
              <div class="col-md-12">
                <button type="button" class="btn btn-success btn-sm" onclick="refreshDatatable()">
                  <i class="fa fa-spinner"></i> Refresh
                </button>
              </div>
            </div>

            {{-- DATA TABLE --}}
            <div class="row">&nbsp;</div>

            <div class="row">
              <div id="tableComplaint" class="col-md-12 col-12 col-lg-12">
                @include('pages.complaints.complaint_pagination')
              </div>
            </div>
            
          </div>

          

        </div>
      </div>

    </div>
  </div>
</section>

<div id="modalAssign" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ModalAssignComplaint" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Assign Pengaduan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-12">
            <form>
              <div class="form-group row">
                <label for="selectAssign" class="col-form-label col-lg-4">
                  Assigned To
                </label>
                <div class="col-lg-8">
                  <select class="form-control" id="selectAssign"></select>
                </div>
              </div>
              <div class="form-group row">
                <div class="offset-lg-4 col-lg-8">
                  <button id="buttonAssign" class="btn btn-primary" data-id="">
                    Apply & Assign
                  </button>
                </div>
              </div>
            </form>     
          </div>
        </div>   
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          Close
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
  <script>
    const urlBackTo = "{{ url('/complaints') }}"
    const urlUsers = "{{ url('/users/roles') }}";
    const urlAssigned = "{{ url('/assigned/complaints') }}";
    const urlPagination = "{!! url('/complaints') !!}"

    function setSelect2(data, select, option = null) {
      let records = [];
      $.each(data, function (i, v) {
        records.push({id: v.id, text: v.name + ' - ' + v.roles[0].name});
      });

      select.empty();
      select.append(new Option());
      
      select.select2({
        dropdownParent: $('#modalAssign'),
        data: records,
        placeholder: "User Operational",
        allowClear: true,
      });

      if(option != null) {
        select.val(option).trigger('change');
      }
    }

    function showAssignModal(id, role_id) {
      $("#modalAssign").modal('show');
      $("#buttonAssign").attr('data-id', id);

      axios.get(urlUsers + '/' + role_id).then((response) => {
        console.log(response);
        if(response.status === 200) {
          const {users} = response.data;
          setSelect2(users, $("#selectAssign"), null);
        }
      }).catch((err) => {
        console.log(err.response);
      });
    }

    function filterDatatable(page) {
      const sentences = $('#sentences').val();
      const search = $('#search').val();

      $.ajax({
        type: "GET",
        url: urlPagination + '?page=' + page + '&sentences=' + sentences + '&search=' + search,
        success: function (data) {  
          $("#tableComplaint").html(data);
        }
      });
    }

    function refreshDatatable() {
      $('select[name="sentences"]').val('all');
      $("#search").val('')
      filterDatatable(1);
    }

    $(function () {

      $("#buttonAssign").click(function (e) { 
        e.preventDefault();
        const data = {
          complaint_id: $(this).attr('data-id'),
          executor_id: $("#selectAssign").val()
        }
        console.log(data);
        

        axios.post(urlAssigned, data).then((response) => {
          console.log(response);
          const {data, status} = response;
          if(status === 200) {
            Swal.fire({
              title: 'SUCCESS',
              text: data.message,
              confirmButtonText: `OK`,
            }).then((result) => {
              if (result.isConfirmed) {
                $("#modalAssign").modal('hide');
                window.location.href = urlBackTo
              }
            })
          }
        }).catch((err) => {
          console.log(err.response);
        })
      });


      $("#sentences").change(function (e) { 
        e.preventDefault();
        var hrefs = $('.pagination a').attr('href');
        var page = 1;

        if(hrefs != '#' && hrefs != '') {
          page = parseInt(hrefs.split('page=')[1]);
        }

        filterDatatable(page)
      });

      $(document).on('click', '.pagination a',function(e)
      {
        e.preventDefault();
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
