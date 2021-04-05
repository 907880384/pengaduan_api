@extends('layouts.auth')

@section('content')
<section class="section">
  <div class="d-flex flex-wrap align-items-stretch">
    <div class="col-lg-4 col-md-6 col-12 order-lg-1 min-vh-100 order-2 bg-white">
      <div class="p-4 m-3">
        <img src="../assets/img/complaint.png" alt="logo" width="80" class="shadow-light rounded-circle mb-5 mt-2">
        <h4 class="text-dark font-weight-normal">{{ config('app.name', 'Pengaduan App') }}</span></h4>
        <p class="text-muted">Silahkan login dengan akun anda</p>
        <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate="">
          @csrf
          <div class="form-group">
            <label for="email">NIP/Username</label>
            <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" tabindex="1" required autofocus  />
            @error('email')
              <div class="invalid-feedback">
                {{ $message }}
              </div>  
            @enderror     
          </div>

          <div class="form-group">
            <div class="d-block">
              <label for="password" class="control-label">Password</label>
            </div>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" tabindex="2" name="password" required>
            @error('password')
              <div class="invalid-feedback">
                {{ $message }}
              </div>
            @enderror
          </div>

          <div class="form-group">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember" {{ old('remember') ? 'checked' : '' }} />
              <label class="custom-control-label" for="remember">Ingatkan Saya</label>
            </div>
          </div>

          <div class="form-group text-right">
            <button type="submit" class="btn btn-primary btn-lg btn-icon icon-right" tabindex="4">
              Login
            </button>
          </div>

          <div class="form-group text-right">
            <span>Download</span>
            <a href="{{ url('download/apk') }}" class="align-center">
              <img src="{{ asset('images/download.png') }}" width="100" height="35" />
            </a>
          </div>
        </form>

      </div>
    </div>
    <div class="col-lg-8 col-12 order-lg-2 order-1 min-vh-100 background-walk-y position-relative overlay-gradient-bottom" data-background="{{ asset('assets/img/gkn.jpg') }}">
      <div class="absolute-bottom-left index-2">
        
      </div>
    </div>
  </div>
</section>
@endsection