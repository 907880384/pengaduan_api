@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Form Pengguna</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item">
        <a href="{{ url('users') }}">List Pengguna</a>
      </div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">Tambah Pengguna Baru</h2>
    <div class="row">
      <div class="col-lg-12 col-md-12 col-12">
        <div class="card">
          <div class="card-body p-5">
            
            <div class="col-lg-6 col-md-6">
              <form>
                @csrf
                <div class="form-group row">
                  <label for="name" class="col-sm-4 col-form-label">Nama Pengguna</label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control" name="name" id="name" value="" />
                  </div>
                </div>

                <div class="form-group row">
                  <label for="username" class="col-sm-4 col-form-label">ID / NIP / Username</label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control" name="username" id="username" value="" />
                  </div>
                </div>

                <div class="form-group row">
                  <label for="password" class="col-sm-4 col-form-label">Default Password</label>
                  <div class="col-sm-8">
                    <input type="password" class="form-control" name="password" id="password" value="12345678" readonly />
                    <div class="text-info">
                      <small>Password Default is 12345678</small>
                    </div>
                  </div>
                </div>
    
                <div class="form-group row">
                  <label for="role_id" class="col-sm-4 col-form-label">Jabatan / Role </label>
                  <div class="col-sm-8">
                    <select name="role_id" id="role_id" class="form-control select2">
                      <option value="-">Pilih Posisi/Role/Jabatan</option>
                      @foreach ($roles as $r)
                          <option value="{{ $r->id }}">
                            {{ $r->name }}
                          </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="form-group row">
                  <div class="col-sm-8 offset-sm-4">
                    <button type="button" class="btn btn-info" onclick="createUser()">
                      <i class="fas fa-save"></i> Save
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

    function createUser(){
      const url = "{!! url('/users') !!}";

      const data = {
        name: $("#name").val(),
        username: $("#username").val(),
        password: $("#password").val(),
        role_id: $("#role_id").val(),
      }

      if(data.role_id != '-' && data.name.trim() != '' && data.username.trim != '') {
        axios.post(url, data).then((response) => {
          const {data, status} = response;
          if(status == 200) {
            Swal.fire({
              title: 'SUCCESS',
              text: data.message,
              confirmButtonText: `OK`,
            }).then((result) => {
              if (result.isConfirmed) {
                window.location.href = "{!! url('/users') !!}"
              }
            })
          }
        }).catch((error) => {
          console.log(error.response);
        });
      }
      else {
        Swal.fire({
          title: 'WARNING',
          text: 'Silahkan isi data dengan benar',
          icon: 'warning'
        });
      }

      
    }

  </script>
@endpush