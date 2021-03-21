@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Informasi Pengaduan</h1>
    <div class="section-header-breadcrumb">
      @if (strtolower(Auth::user()->roles()->first()->slug) == 'customer')
      <div class="breadcrumb-item">
        <a href="{{ url('/complaints/create') }}" class="btn btn-primary btn-sm">
          <i class="fas fa-plus"></i> Tambah Pengaduan
        </a>
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
                  <label for="sentences" class="col-md-3 m-2">
                    Aktivitas
                  </label>
                  <div class="col-md-8">
                    <select id="sentences" name="sentences" class="form-control form-control-sm">
                      @foreach ($activities as $item)
                        <option value="{{ $item['type'] }}">{{ Str::ucfirst($item['value']) }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group row">
                  <label for="search" class="col-md-3 m-2">Pencarian</label>
                  <div class="col-md-8">
                    <input type="text" id="search" class="form-control" /> 
                  </div>
                </div>
              </div>

              <div class="col-md-6">
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

            {{-- DATA TABLE --}}
            <div class="row">&nbsp;</div>

            <div class="row p-3">
              @include('pages.complaints.complaint_pagination')
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

    function setDatatable(tableName, filter = null) {
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
        "url": "{{ route('list.complaints') }}",
        "data": function (d) {

          console.log("Filter", filter);

          if(filter == null) {
            d.search = null;
            d.sentences = null;
          }
          else {            
            d.search = filter.search;
            d.sentences = filter.sentences;
          }
        }   
      },
      columns: [
        {data: 'DT_RowIndex',  name: 'DT_RowIndex', className: 'text-center'},
        {data: 'messages', name: 'messages'},
        {
          data: 'is_finished', 
          name: 'is_finished', 
          className: 'text-center',
          render: function(data, type, row) {
            console.log(row);
            if (data)
              return '<div class="badge badge-primary">SELESAI</div>';
            else {
              return '<div class="badge badge-secondary">BELUM SELESAI</div>';
            }
          }
        },
        {
          data: 'sender_name', 
          name: 'sender_name', 
          className: 'text-center',
          render: function(data) {
            return `<b>${data}</b>`
          }
        },
        {data: 'types_name', name: 'types_name', className: 'text-center'},
        {
          data: 'executor', 
          name: 'executor', 
          className: 'text-center',
          render: function(data) {
            return data != null ? '<b>' + data.name + '</b>' : '';
          }
        },
        {data: 'action', name: 'action', className: 'text-center'}
      ],
      "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
    });

    }

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
      console.log("Role ID", role_id);
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
      setDatatable('complaintTable', {
        sentences: $('#sentences').val(),
        search: $('#search').val(),
      });
    }

    function refreshDatatable() {
      $('select[name="sentences"]').val('all');
      $("#search").val('')
      setDatatable('complaintTable')
    }

    $(function () {

      setDatatable('complaintTable')


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

    });
  </script>
@endpush
