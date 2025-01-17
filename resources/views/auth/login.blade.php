<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Bima Buy</title>
    <link rel="stylesheet" href="{{asset('vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendors/css/vendor.bundle.base.css')}}">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <link rel="shortcut icon" href="{{asset('storage/profile_photos/default_photos/websitelogo.png')}}" />
    <style>
        .icon {
            position: absolute;
            right: 13px;
            top: 13px;
            color: #b66dff;
            font-size: 20px;
            cursor: pointer;
        }

        .error-message {
            width: 100%;
            margin-top: 0.30rem;
            font-size: 0.875em;
            color: #fe7c96;
        }
        .success-message{
            width: 100%;
            margin-top: 0.30rem;
            font-size: 0.875em;
            color: #42992f;
        }

        .error-input {
            border-color: #fe7c96;
        }
    </style>
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth">
                <div class="row flex-grow">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left p-5">
                            <div class="brand-logo">
                                <!-- <img src="{{asset('images/logo.svg')}}"> -->
                                <h1 class="py-2"
                                    style="font-weight: bold;color: white;background: linear-gradient(to right, #da8cff, #9a55ff);text-align: center;">
                                    Bima Buy</h1>
                            </div>
                            <h4>Hello! let's get started</h4>
                            <h6 class="font-weight-light">Sign in to continue.</h6>
                            @if(session('status'))
                            <div class="success-message">
                                {{ session('status') }}
                            </div>
                            @endif

                            <!-- Display error message -->
                            @if($errors->any())
                            <div class="error-message">
                                @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                                @endforeach
                            </div>
                            @endif
                            <form class="pt-3" method="POST" action="{{ route('login.post') }}">
                                @csrf
                                <div class="form-group">
                                    <input type="text" name="mobile"
                                        class="form-control form-control-lg @error('mobile') error-input @enderror"
                                        id="exampleInputMobile" placeholder="Enter mobile number"
                                        value="{{ old('mobile') }}">
                                    @error('mobile')
                                        <span class="error-message" role="alert">
                                            <strong class="ms-2">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group position-relative">
                                    <input type="password" name="password"
                                        class="form-control form-control-lg @error('password') error-input @enderror"
                                        id="exampleInputPassword1" placeholder="Enter password">
                                    @error('password')
                                        <span class="error-message" role="alert">
                                            <strong class="ms-2">{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <i class="mdi mdi-eye icon" id="togglePassword"></i>
                                </div>
                                <div class="my-2 d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <label class="form-check-label text-muted">
                                            <input type="checkbox" class="form-check-input" name="remember" {{ old('remember') ? 'checked' : '' }}> Keep me signed in
                                        </label>
                                    </div>
                                    <a href="{{ route('password.request') }}" class="auth-link text-black">Forgot password?</a>
                                </div>
                                <div class="mt-3">
                                    <button type="submit"
                                        class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">SIGN
                                        IN</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="{{asset('vendors/js/vendor.bundle.base.js')}}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{asset('js/off-canvas.js')}}"></script>
    <script src="{{asset('js/hoverable-collapse.js')}}"></script>
    <script src="{{asset('js/misc.js')}}"></script>
    <script>
        $('#togglePassword').on('click', function () {
            var passwordField = $('#exampleInputPassword1');
            var icon = $('#togglePassword');

            // Toggle the type attribute
            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                icon.removeClass('mdi-eye').addClass('mdi-eye-off');
            } else {
                passwordField.attr('type', 'password');
                icon.removeClass('mdi-eye-off').addClass('mdi-eye');
            }
        });

    </script>
    <!-- endinject -->
</body>

</html>