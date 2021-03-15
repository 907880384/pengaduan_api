@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Form Pengaduan</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item">
        <a href="{{ url('complaints') }}" class="btn btn-primary btn-sm">
          <i class="fas fa-clipboard-list"></i> LIST PENGADUAN
        </a>
      </div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">Buat Pengaduan</h2>
    <div class="row">
      <div class="col-lg-12 col-md-12 col-12">
        <div class="card">
          <div class="card-body p-5">
            
            <div class="col-lg-12 col-md-12">
              <form>
                @csrf
                <div class="form-group row">
                  <label for="selectAssignTo" class="col-sm-4 col-form-label">
                    Tujuan Pengaduan
                  </label>
                  <div class="col-sm-8">
                    <select id="selectAssignTo" class="form-control select2">
                      <option value="-">Pilih Tujuan Pengaduan</option>
                      @foreach ($roles as $r)
                          <option value="{{ $r->id }}">
                            {{ $r->name }}
                          </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                {{-- <div class="form-group row">
                  <label for="titleComplaint" class="col-sm-4 col-form-label">
                    Uraian (Judul)
                  </label>
                  <div class="col-sm-8">
                    <input type="text" name="titleComplaint" id="titleComplaint" class="form-control" />
                  </div>
                </div> --}}

                <div class="form-group row">
                  <label for="messageComplaint" class="col-sm-4 col-form-label">
                    Uraian Pengaduan
                  </label>
                  <div class="col-sm-8">
                    <textarea id="messageComplaint" class="form-control form-control-sm" rows="10" cols="30"></textarea>
                  </div>
                </div>

                {{-- <div class="form-group row">
                  <label class="col-sm-4 col-form-label">
                    Mode Pengaduan
                  </label>
                  <div class="col-sm-8">
                    <div class="mt-2">
                      <input type="radio" name="selectMode" value="urgent"> Urgent &nbsp;
                      <input type="radio" name="selectMode" value="normal"> Normal
                    </div>
                      
                  </div>
                </div> --}}
    
                <div class="form-group row">
                  <div class="col-sm-8 offset-sm-4">
                    <button type="button" class="btn btn-primary" onclick="sendComplaint()">
                      <i class="fas fa-save"></i> Kirim Pengaduan
                    </button>
                  </div>
                </div>
              </form>
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

    const urlTypeByRole = "{{ url('categories/complaint/roles') }}";
    const urlComplaints = "{{ url('complaints') }}"

    function sendComplaint() {
      const data = {
        // title: $('#titleComplaint').val(),
        messages: $('#messageComplaint').val(),
        // is_urgent: $('input[name="selectMode"]:checked').val() == 'urgent' ? true : false,
        type_id: $('#selectAssignTo').val()
      }
      
      axios.post(urlComplaints, data).then((response) => {
        const {data,status} = response;

        if(status === 200) {
          Swal.fire({
            title: 'SUCCESS',
            text: data.message,
            confirmButtonText: `OK`,
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = urlComplaints
            }
          });

        }
      }).catch((err) => console.log(err.response));
    }
  </script>
@endpush