<!DOCTYPE html>
<html lang="tr">

<head>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Selimcan Gürsu | Full Stack Web Developer" name="author">
    <link href="{{ asset('assets/images/logo/favicon.png') }}" rel="icon" type="image/x-icon">
    <link href="{{ asset('assets/images/logo/favicon.png') }}" rel="shortcut icon" type="image/x-icon">
    <title>Erişim Yetkiniz Bulunmamaktadır | Dijistack ERP</title>
    <link href="{{ asset('assets/vendor/fontawesome/css/all.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com/" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&amp;display=swap"
        rel="stylesheet">
    <link href="{{ asset('assets/vendor/tabler-icons/tabler-icons.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/responsive.css') }}" rel="stylesheet" type="text/css">

</head>

<body>

    <div class="error-container p-0">
        <div class="container">
            <div>
                <div>
                    <img alt="Erişim Yetkiniz Yok" class="img-fluid"
                        src="{{ asset('assets/images/background/error-403.png') }}">
                </div>
                <div class="mb-3">
                    <div class="row">
                        <div class="col-lg-8 offset-lg-2">
                            <p class="text-center text-secondary f-w-500 mt-4">
                                Erişim Yetkiniz Yok.<br>
                                Bu sayfaya ulaşmak için gerekli izinlere sahip değilsiniz.
                            </p>
                        </div>
                    </div>
                </div>
                <a class="btn btn-lg btn-primary" href="javascript:void(0);" onclick="history.back()" role="button">
                    <i class="ti ti-home"></i> Geriye Dön
                </a>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/jquery-3.6.3.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
