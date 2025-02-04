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
                            <h6 class="font-weight-light">Forget Password.</h6>
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
                            <form action="{{ route('password.email') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <input type="text" name="email" class="form-control form-control-lg" id="email"
                                        placeholder="Enter your email" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Send Password Reset Link</button>
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
    <script src="{{asset('js/off-canvas.js')}}"></script>
    <script src="{{asset('js/hoverable-collapse.js')}}"></script>
    <script src="{{asset('js/misc.js')}}"></script>


    <!-- endinject -->
</body>

</html>