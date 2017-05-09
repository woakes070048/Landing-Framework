<!DOCTYPE html>
<html lang="{{ \App::getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" type="image/x-icon" href="{{ \Platform\Controllers\Core\Reseller::get()->favicon }}" />

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ \Platform\Controllers\Core\Reseller::get()->name }}</title>

  <!-- Styles -->
  <link href="{{ asset('assets/css/styles.min.css') }}" rel="stylesheet">

  <!-- Scripts -->
  <script src="{{ url('assets/javascript?lang=' . \App::getLocale()) }}"></script>
  <script>var app_root = "{{ url('/') }}";</script>

  <script>
    window.Laravel = <?php echo json_encode([
      'csrfToken' => csrf_token(),
    ]); ?>
  </script>

  @yield('head')
</head>
<body>

@yield('content')

  <!-- Scripts -->
  <script src="{{ url('assets/js/scripts.min.js') }}"></script>

  <!-- Fonts -->
  <link href="//fonts.googleapis.com/css?family=Roboto:400,500,700" rel="stylesheet">
  <link href="//fonts.googleapis.com/css?family=Source+Sans+Pro:600,400,700" rel="stylesheet">

@yield('bottom')

  <div id="deviceWarning">
    <div>
      <p class="lead">{{ trans('global.device_warning1') }}</p>
      <p class="lead">{{ trans('global.device_warning2') }}</p>
      <p class="lead"><button type="button" class="btn btn-lg btn-primary" onclick="$('#deviceWarning').remove()">{{ trans('global.device_warning_remove') }}</button></p>
    </div>
  </div>
</body>
</html>