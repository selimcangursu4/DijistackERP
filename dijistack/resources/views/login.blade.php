<!DOCTYPE html>
<html lang="tr">

<head>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Selimcan Gürsu | Full Stack Web Developer" name="author">
    <link href="{{ asset('assets/images/logo/favicon.png') }}" rel="icon" type="image/x-icon">
    <link href="{{ asset('assets/images/logo/favicon.png') }}" rel="shortcut icon" type="image/x-icon">
    <title>Giriş Yap | Dijistack ERP</title>
    <link href="{{ asset('assets/vendor/fontawesome/css/all.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com/" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&amp;display=swap"
        rel="stylesheet">
    <link href="{{ asset('assets/vendor/tabler-icons/tabler-icons.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/vendor/phosphor/phosphor-bold.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/responsive.css') }}" rel="stylesheet" type="text/css">
</head>

<body class="sign-in-bg">
    <div class="app-wrapper d-block">
        <div class="">
            <div class="container main-container">
                <div class="row main-content-box">
                    <div class="col-lg-7 image-content-box d-none d-lg-block">
                        <div class="form-container">
                            <div class="signup-content mt-4">
                                <span>
                                    <h1 class="text-center">DİJİSTACK
                                        <br>
                                        Yazılım Çözümleri
                                    </h1>
                                </span>
                            </div>
                            <div class="signup-bg-img">
                                <img alt="" class="img-fluid" src="../assets/images/login/01.png">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 form-content-box">
                        <div class="form-container ">
                           <form action="{{ url('/') }}" method="POST" class="app-form">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-5 text-center text-lg-start">
                                            <h2 class="text-white f-w-600">Hoşgeldiniz! <span
                                                    class="text-dark">Dijistack ERP</span> </h2>
                                            <p class="f-s-16 mt-2">İş süreçlerinizi dijitalleştirerek, verimliliği ve
                                                kontrolü artıran modern ERP çözümü.</p>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                           <input class="form-control" id="email" name="email" type="email" placeholder="E-Posta Adresi" required>
                                            <label for="UserName">E-Posta Adresi</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                           <input class="form-control" id="password" name="password" type="password" placeholder="Parolanız" required>
                                            <label for="floatingInput">Parola</label>
                                        </div>
                                        <div class="text-end ">
                                            <a class="text-dark f-w-500 text-decoration-underline"
                                                href="forgot_password.html">Şifremi Unuttum</a>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check d-flex align-items-center gap-2 mb-3">
                                            <input class="form-check-input w-25 h-25" id="checkDefault" type="checkbox"
                                                value="">
                                            <label class="form-check-label text-white mt-2 f-s-16 text-dark"
                                                for="checkDefault">
                                                Beni Hatırla
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <button class="btn btn-primary btn-lg w-100" type="submit">Giriş
                                            Yap</button>
                                    </div>


                                    <div class="app-divider-v light justify-content-center py-lg-5 py-3">
                                        <p>Bizi Takip Edin !</p>
                                    </div>

                                    <div class="col-12">
                                        <div class="d-flex gap-3 justify-content-center text-center">
                                            <button class="btn btn-light-white  icon-btn w-45 h-45 b-r-15 "
                                                type="button">
                                                <i class="ph-bold ph-facebook-logo f-s-20"></i>
                                            </button>
                                            <button class="btn btn-light-white  icon-btn w-45 h-45 b-r-15 "
                                                type="button">
                                                <i class="ph-bold  ph-google-logo f-s-20"></i>
                                            </button>
                                            <button class="btn btn-light-white  icon-btn w-45 h-45 b-r-15 "
                                                type="button">
                                                <i class="ph-bold  ph-twitter-logo f-s-20"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/js/jquery-3.6.3.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
