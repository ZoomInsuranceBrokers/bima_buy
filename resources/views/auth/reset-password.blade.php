<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Bima Buy - Reset Password</title>
    <link rel="stylesheet" href="{{asset('vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('vendors/css/vendor.bundle.base.css')}}">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <link rel="shortcut icon" href="{{asset('storage/profile_photos/default_photos/websitelogo.png')}}" />
    <style>
        /* Custom Styles */
        body {
            background-color: #f4f7fc;
            font-family: 'Roboto', sans-serif;
        }

        .container-scroller {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .auth-form-light {
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .brand-logo h1 {
            color: #9a55ff;
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
        }

        .auth-form-light h6 {
            text-align: center;
            font-size: 1rem;
            color: #555;
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 8px;
            padding: 15px;
            font-size: 1rem;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #9a55ff;
            box-shadow: 0 0 5px rgba(154, 85, 255, 0.5);
        }

        .btn-primary {
            background-color: #9a55ff;
            color: white;
            font-size: 1.2rem;
            font-weight: 600;
            padding: 15px 0;
            border: none;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #7c38cc;
        }

        .success-message,
        .error-message {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1rem;
            padding: 10px;
            border-radius: 5px;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
        }
        .btn-block{
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container-scroller">
        <div class="auth-form-light text-left p-5">
            <div class="brand-logo">
                <h1>Bima Buy</h1>
            </div>
            <h6 class="font-weight-light">Reset Your Password</h6>

            <!-- Success or Error Message -->
            @if(session('status'))
                <div class="success-message">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="error-message">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- New Password Field -->
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="New Password" required>
                </div>

                <!-- Confirm Password Field -->
                <div class="form-group">
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
            </form>
        </div>
    </div>

    <!-- JS Scripts -->
    <script src="{{asset('vendors/js/vendor.bundle.base.js')}}"></script>
    <script src="{{asset('js/off-canvas.js')}}"></script>
    <script src="{{asset('js/hoverable-collapse.js')}}"></script>
    <script src="{{asset('js/misc.js')}}"></script>
</body>
</html>
