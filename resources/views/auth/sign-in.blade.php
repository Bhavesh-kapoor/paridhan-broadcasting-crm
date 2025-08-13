<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Title -->
    <title> Paridhan | Login</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ config('contants.logo') }}">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- file upload -->
    <link rel="stylesheet" href="{{ asset('assets/css/file-upload.css')}}">
    <!-- file upload -->
    <link rel="stylesheet" href="{{ asset('assets/css/plyr.css')}}">
    <!-- full calendar -->
    <!-- jquery Ui -->
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css')}}">
    <!-- editor quill Ui -->
    <!-- Main css -->
    <link rel="stylesheet" href="{{ asset('assets/css/main.css')}}">
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
</head>

<body>

    <!--==================== Sidebar Overlay End ====================-->
    <div class="side-overlay"></div>
    <!--==================== Sidebar Overlay End ====================-->

    <section class="auth d-flex">
        <div class="auth-left bg-main-50 flex-center p-24 ">
            <img src="{{ asset('assets/logo/banner.jpeg') }}" style="width: 900px; height:700px;object-fit:contain" alt="">

        </div>
        <div class="auth-right py-40 px-24 flex-center flex-column">
            <div class="auth-right__inner mx-auto w-100">
                <a href="{{route('login')}}" class="auth-right__logo">
                    <img src="{{ config('contants.logo') }}" alt="">
                </a>
                <h2 class="mb-8">Welcome Back! &#128075;</h2>

                <form action="{{ route('login.validate') }}" method="post" id="loginform">
                    @csrf()
                    <div class="mb-24">
                        <label for="email" class="form-label mb-8 h6">Email or Username</label>
                        <div class="position-relative">
                            <input type="text" name="email" class="form-control py-11 ps-40" id="email" placeholder="Type your username">
                            <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-user"></i></span>

                        </div>
                        <div class="error text-danger" id="email-error"></div>

                    </div>
                    <div class="mb-24">
                        <label for="password" class="form-label mb-8 h6"> Password</label>
                        <div class="position-relative">
                            <input type="password" name="password" class="form-control py-11 ps-40" id="password" placeholder="Enter  Password" value="">
                            <span class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y ph ph-eye-slash" id="#current-password"></span>
                            <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-lock"></i></span>


                        </div>
                        <div class="error text-danger" id="password-error"></div>
                        <div class="error text-danger" id="message-error"></div>

                    </div>
                    <div class="mb-32 flex-between flex-wrap gap-8">
                        <div class="form-check mb-0 flex-shrink-0">
                            <input class="form-check-input flex-shrink-0 rounded-4" value="1" name="remember" type="checkbox"  id="remember">
                            <label class="form-check-label text-15 flex-grow-1" for="remember">Remember Me </label>
                        </div>
                        <a href="forgot-password.html" class="text-main-600 hover-text-decoration-underline text-15 fw-medium">Forgot Password?</a>
                    </div>
                    <button type="submit" class="btn btn-main rounded-pill w-100" id="loginbutton">Sign In</button>


                </form>
            </div>
        </div>
    </section>

    <!-- Jquery js -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js')}}"></script>
    <!-- Bootstrap Bundle Js -->
    <script src="{{ asset('assets/js/boostrap.bundle.min.js')}}"></script>
    <!-- Phosphor Js -->
    <script src="{{ asset('assets/js/phosphor-icon.js')}}"></script>
    <!-- file upload -->
    <script src="{{ asset('assets/js/file-upload.js')}}"></script>
    <!-- file upload -->
    <script src="{{ asset('assets/js/plyr.js')}}"></script>
    <!-- full calendar -->
    <script src="{{ asset('assets/js/full-calendar.js')}}"></script>
    <!-- jQuery UI -->
    <script src="{{ asset('assets/js/jquery-ui.js')}}"></script>
    <!-- jQuery UI -->
    <script src="{{ asset('assets/js/editor-quill.js')}}"></script>
    <!-- apex charts -->
    <script src="{{ asset('assets/js/apexcharts.min.js')}}"></script>
    <!-- jvectormap Js -->
    <script src="{{ asset('assets/js/jquery-jvectormap-2.0.5.min.js')}}"></script>
    <!-- jvectormap world Js -->
    <script src="{{ asset('assets/js/jquery-jvectormap-world-mill-en.js')}}"></script>

    <!-- main js -->
    <script src="{{ asset('assets/js/main.js')}}"></script>
    <script src="{{ asset('functions/login.js')}}"></script>



</body>

</html>