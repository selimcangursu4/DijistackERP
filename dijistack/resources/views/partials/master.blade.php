<!DOCTYPE html>
<html lang="tr">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Selimcan Gürsu | Full Stack Web Developer" name="author">
    <link href="{{asset('assets/images/logo/logo.png')}}" rel="icon" type="image/x-icon">
    <link href="{{asset('assets/images/logo/logo.png')}}" rel="shortcut icon" type="image/x-icon">
    <title>Dijistack ERP | Yeni Nesil Admin Paneli</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <link href="{{asset('assets/vendor/animation/animate.min.css')}}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com/" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&amp;display=swap"
          rel="stylesheet">
    <link href="{{asset('assets/vendor/flag-icons-master/flag-icon.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/vendor/tabler-icons/tabler-icons.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/vendor/apexcharts/apexcharts.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/custom.css')}}" rel="stylesheet">
    <link href="{{asset('assets/vendor/bootstrap/bootstrap.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/vendor/simplebar/simplebar.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('assets/css/style.css')}}" rel="stylesheet" type="text/css">
    
    <link href="{{asset('assets/css/responsive.css')}}" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="app-wrapper">
    <div class="loader-wrapper">
        <div class="loader_24"></div>
    </div>
    @include('partials.sidebar')
    <div class="app-content">
        <div class="">
            @include('partials.header')
            <main style="height: auto;min-height:100vh">
                @yield('main')
            </main>
        </div>
    </div>
    <div class="go-top">
      <span class="progress-value">
        <i class="ti ti-chevron-up"></i>
      </span>
    </div>
    @include('partials.footer')
</div>
<div id="customizer"></div>
<script src="{{asset('assets/vendor/bootstrap/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('assets/vendor/simplebar/simplebar.js')}}"></script>
<script src="{{asset('assets/vendor/phosphor/phosphor.js')}}"></script>
<script src="{{asset('assets/vendor/glightbox/glightbox.min.js')}}"></script>
<script src="{{asset('assets/vendor/apexcharts/apexcharts.min.js')}}"></script>
<script src="{{asset('assets/js/customizer.js')}}"></script>
<script src="{{asset('assets/js/ecommerce_dashboard.js')}}"></script>
<script src="{{asset('assets/js/script.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</body>
</html>