@extends('layouts.backend')

@section('content')
<section class="section">
  <div class="section-header">
    <h1>Form Buku Tamu</h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item">
        <a href="{{ url('visitors') }}" class="btn btn-primary btn-sm">
          <i class="fas fa-clipboard-list"></i> LIST TAMU
        </a>
      </div>
    </div>
  </div>

  <div class="section-body">
    <h2 class="section-title">Daftar Tamu Masuk</h2>
    <div class="row">
      <div class="col-lg-12 col-md-12 col-12">
        <div class="card">
          <div class="card-body mt-4">
            <form>
              @csrf
              <div class="row">
                
                  <div class="col-md-6">
                    
                    <!-- VISITOR NAME -->
                    <div class="form-group row">
                      <label for="visitorName" class="col-sm-4 col-form-label">Nama Lengkap</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control form-control-sm" name="visitorName" id="visitorName" value="" />
                      </div>
                    </div>

                    <!-- VISITOR TYPE IDENTITY -->
                    <div class="form-group row">
                      <label for="visitorTypeIdentity" class="col-sm-4 col-form-label">Jenis Identitas</label>
                      <div class="col-sm-8">
                        <select name="visitorTypeIdentity" id="visitorTypeIdentity" class="form-control" placeholder="Pilih Jenis Identitas">
                          @foreach ($typeIdentities as $item)
                            <option value="{{ $item->id }}">
                              {{ $item->name }}
                            </option>
                          @endforeach
                        </select>
                      </div>
                    </div>

                    <!-- VISITOR IDENTITY -->
                    <div class="form-group row">
                      <label for="visitorNoIdentity" class="col-sm-4 col-form-label">No. Identitas</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control form-control-sm" name="visitorNoIdentity" id="visitorNoIdentity" value="" />
                      </div>
                    </div>

                    <!-- VISITOR PHONE -->
                    <div class="form-group row">
                      <label for="visitorPhone" class="col-sm-4 col-form-label">No. HP</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control form-control-sm" name="visitorPhone" id="visitorPhone" value="" />
                      </div>
                    </div>

                    <!-- VISITOR PURPOSE (TUJUAN) -->
                    <div class="form-group row">
                      <label for="visitorPurpose" class="col-sm-4 col-form-label">Tujuan</label>
                      <div class="col-sm-8">
                        <textarea class="form-control form-control-sm" rows="4" name="visitorPurpose" id="visitorPurpose"></textarea>
                      </div>
                    </div>

                    <!-- VISITOR DESCRIPTION -->
                    <div class="form-group row">
                      <label for="visitorDescription" class="col-sm-4 col-form-label">Keterangan</label>
                      <div class="col-sm-8">
                        <textarea class="form-control form-control-sm" rows="4" name="visitorDescription" id="visitorDescription"></textarea>
                      </div>
                    </div>

                    <div class="form-group row">
                      <div class="col-sm-8 offset-sm-4">
                        <button type="button" id="btnSaveVisitor" class="btn btn-primary btn-sm">
                          <i class="fas fa-save"></i> SIMPAN
                        </button>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="row">
                      <div class="col-md-12 col-lg-12">
                        <button type="button" id="btnOpenCamera" class="btn btn-info btn-sm">
                          <i class="fas fa-camera"></i> Buka Kamera
                        </button>

                        <button type="button" id="btnCloseCamera" class="btn btn-danger btn-sm" hidden="true">
                          <i class="fas fa-power-off"></i> Tutup Kamera
                        </button>

                        <button type="button" id="btnSnapPicture" class="btn btn-primary btn-sm" hidden="true">
                          <i class="fas fa-plus-square"></i> Ambil Gambar
                        </button>

                        <button type="button" id="btnResetAll" class="btn btn-success btn-sm" hidden="true">
                          <i class="fa fa-spinner"></i> Reset Gambar
                        </button>

                      </div>

                      <div class="clearfix">&nbsp;</div>

                      <div id="myCamera" class="col-md-12 col-lg-12 border-darken-1"></div>

                      <div id="snapImage" class="col-md-12"></div>
                    </div>
                  </div>
                
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/uuid/8.3.2/uuid.min.js"></script>
  <script>
    let urlApi = "{{ url('visitors') }}"
    
    let visitors = {
      imageFiles: [],
    }

    //Initial Webcam
    Webcam.set({
      width: 480,
      height: 380,
      dest_width: 640,
      dest_height: 480,
      image_format: 'jpeg',
      jpeg_quality: 90,
      force_flash: false
    });

    function deleteImages(id) {   
      visitors.imageFiles = visitors.imageFiles.filter((x) => x.id != id, []);
      setImagePreview(visitors.imageFiles);

      if(visitors.imageFiles.length < 2) {
        Webcam.attach('myCamera');
        $("#btnOpenCamera").attr("hidden", true);
        $("#btnSnapPicture").attr("hidden", false);
        $("#btnCloseCamera").attr("hidden", false);
        document.getElementById("myCamera").style.display = '';
      }
    }

    function setImagePreview(images) {
      var strHTML = '';

      if(images.length > 0) {
        for (let i = 0; i < images.length; i++) {
          const element = images[i];
          strHTML += '<p><img src="' + element.uri + '" width="320" height="240" /> <button type="button" class="btn btn-danger btn-sm" onclick="deleteImages(\'' + element.id + '\')"><i class="fas fa-times"></i> Hapus</button></p>'
        }
      }
      document.getElementById('snapImage').innerHTML = strHTML;
    }


    $(function () {

      document.getElementById("myCamera").style.display = 'none';

      //Change Type Identity
      $('#visitorTypeIdentity').change(function (e) { 
        e.preventDefault();
        $("#visitorNoIdentity").val('')
      });

      //Open Camera
      $("#btnOpenCamera").click(function (e) { 
        e.preventDefault();
        document.getElementById("myCamera").style.display = '';
        Webcam.attach('myCamera');
        $(this).attr("hidden", true);
        $("#btnSnapPicture").attr("hidden", false);
        $("#btnCloseCamera").attr("hidden", false);
      });

      //Close Camera
      $("#btnCloseCamera").click(function (e) { 
        e.preventDefault();
        Webcam.reset();
        $(this).attr("hidden", true);
        $("#btnSnapPicture").attr("hidden", true);
        $('#btnOpenCamera').attr("hidden", false);
        document.getElementById("myCamera").style.display = 'none';
      });

      //Snap Picture
      $("#btnSnapPicture").click(function (e) { 
        Webcam.snap(function(dataUri) {
          visitors.imageFiles.push({ id: uuid.v4(), uri: dataUri});
        });
        
        const takePic = visitors.imageFiles.length == 2 ? "Kedua" : "Pertama";
        Swal.fire({
          title: "SUCCESS INFO",
          text: `Pengambilan Gambar ${takePic} Berhasil`,
          icon: "success",
        });


        if(visitors.imageFiles.length == 2) {
          Webcam.reset();
          setImagePreview(visitors.imageFiles);
          $("#btnCloseCamera").attr("hidden", true);
          $("#btnSnapPicture").attr("hidden", true);
          $("#btnOpenCamera").attr("hidden", false);  
          $("#btnResetAll").attr("hidden", false);      
          document.getElementById("myCamera").style.display = 'none';
        } 
      });

      //Reset Gambar
      $("#btnResetAll").click(function (e) { 
        e.preventDefault();
        if(visitors.imageFiles.length == 2) {
          visitors.imageFiles = [];
          setImagePreview(visitors.imageFiles);
          $("#btnResetAll").attr("hidden", true);
        }
      });

      //Save Visitor
      $("#btnSaveVisitor").click(function (e) { 
        e.preventDefault();

        var fdata = new FormData();

        fdata.append('_token', '{{csrf_token()}}');
        fdata.append('nama', $("#visitorName").val());
        fdata.append('tipe_identitas', $("#visitorTypeIdentity").val());
        fdata.append('no_identitas', $("#visitorNoIdentity").val());
        fdata.append('no_hp', $("#visitorPhone").val());
        fdata.append('tujuan', $("#visitorPurpose").val());
        fdata.append('keterangan', $("#visitorDescription").val());
        fdata.append('dataImage', JSON.stringify(visitors.imageFiles))
        
        console.log(visitors.imageFiles);

        if(visitors.imageFiles.length > 0) {
          axios.post(urlApi, fdata).then((response) => {
            console.log("response", response)

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
            console.log("Error", error.response)
            const {data, status} = error.response;

            Swal.fire({
              title: 'GAGAL',
              text: data.message,
              icon: 'error',
            });
          });
        }
        else {
          Swal.fire({
            title: 'PERINGATAN',
            text: 'Mohon lengkapi data tamu dengan benar',
            icon: 'warning',
            confirmButtonText: `OK`,
          });
        }
      });

      
    });


  </script>
@endpush