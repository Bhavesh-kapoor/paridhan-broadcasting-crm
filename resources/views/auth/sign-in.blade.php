<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="shortcut icon" href="{{ url(config('contants.logo')) }}">
    <!-- loader -->
    <link href="{{ asset('/assets/css/pace.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('/assets/js/pace.min.js') }}"></script>
    <!-- Bootstrap CSS -->
    <link href="{{ asset('/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link href="{{ asset('/assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('/assets/css/icons.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/assets/plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/plugins/toastr/toastr.min.css') }}">
    <title>Paridhan | Login</title>
    <style>
        #password-error {
            position: absolute;
            top: 95%;
        }
    </style>
    <script>
        var base_url = "{{ URL('/') }}";
    </script>
</head>

<body>
    <!--wrapper-->
    <div class="wrapper">
        <div class="section-authentication-cover">
            <div class="">
                <div class="row g-0">
                    <div class="col-12 col-xl-7 col-xxl-8  align-items-center justify-content-center d-none d-xl-flex">
                        <div class="card shadow-none bg-transparent shadow-none rounded-0 mb-0">
                            <div class="card-body">
                                <img src="{{ asset('assets/logo/banner.jpeg') }}"
                                    class="img-fluid auth-img-cover-login" width="650" alt="" />
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-5 col-xxl-4 auth-cover-right align-items-center justify-content-center">
                        <div class="card rounded-0 m-3 shadow-none bg-transparent mb-0">
                            <div class="card-body p-sm-5">
                                <div class="">
                                    <div class="mb-3 text-center">
                                        <img src="{{ url(config('contants.logo')) }}" class="img-fluid" alt="">
                                    </div>
                                    <div class="text-center mb-4">
                                        <h2 class="mb-8">Welcome Back! &#128075;</h2>
                                    </div>
                                    <div class="form-body">
                                        <form action="" class="auth_form login_form row g-3" id="login_form"
                                            autocomplete="off">
                                            @csrf
                                            <div class="col-12">
                                                <label for="email" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="email" name="email"
                                                    placeholder="jhon@example.com" required>
                                            </div>
                                            <div class="col-12">
                                                <label for="password" class="form-label">Password</label>
                                                <div class="input-group" id="show_hide_password">
                                                    <input type="password" class="form-control border-end-0"
                                                        id="password" name="password" placeholder="Enter Password"
                                                        required>
                                                    <div>
                                                        <a href="javascript:;" class="input-group-text bg-transparent">
                                                            <i class="bx bx-hide"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-primary submit-btn">Sign
                                                        in</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end row-->
            </div>
        </div>
    </div>
    <!--end wrapper-->
    <!-- Bootstrap JS -->
    <script src="{{ asset('/assets/js/bootstrap.bundle.min.js') }}"></script>
    <!-- plugins -->
    <script src="{{ asset('/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <!--Password show & hide js -->
    <script>
        $(document).ready(function() {
            // Password show/hide functionality
            $("#show_hide_password a").on('click', function(event) {
                event.preventDefault();
                if ($('#show_hide_password input').attr("type") == "text") {
                    $('#show_hide_password input').attr('type', 'password');
                    $('#show_hide_password i').addClass("bx-hide");
                    $('#show_hide_password i').removeClass("bx-show");
                } else if ($('#show_hide_password input').attr("type") == "password") {
                    $('#show_hide_password input').attr('type', 'text');
                    $('#show_hide_password i').removeClass("bx-hide");
                    $('#show_hide_password i').addClass("bx-show");
                }
            });

            // jQuery Validation setup
            $("#login_form").validate({
                errorClass: "text-danger validation-error",
                rules: {
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true
                    }
                },
                messages: {
                    email: {
                        required: "Please enter your email",
                        email: "Please enter a valid email address"
                    },
                    password: {
                        required: "Please enter your password"
                    }
                },
                showErrors: function(errorMap, errorList) {
                    this.defaultShowErrors();
                    if ($("#password-error").is(":visible")) {
                        $(".submit-btn").addClass("mt-3");
                    } else {
                        $(".submit-btn").removeClass("mt-3");
                    }
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    var formData = new FormData(form);

                    $.ajax({
                        url: base_url + '/check-login',
                        type: 'POST',
                        data: formData,
                        cache: false,
                        processData: false,
                        contentType: false,
                        dataType: "json",
                        success: function(response) {
                            if (response.status == true) {
                                toastr.success(response.message);
                                window.location.href = response.redirect;
                            } else if (response.status == 'validation_error') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validation Error',
                                    html: response.message
                                });
                            } else if (response.status == false) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message
                                });
                            } else {
                                toastr.error('Something went wrong. Please try again.');
                            }
                        },
                        error: function(error) {
                            toastr.error('Server error. Please try again.');
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>
