<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>LOGIN - {{ config('app.name', 'Pengaduan App') }}</title>
  
  <!-- Styles -->
  <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">  
</head>

<body>
  <div id="app">
    @yield('content')
  </div>

  <!-- Styles -->
  <script src="{{ asset('js/auth.js') }}"></script>

</body>
</html>