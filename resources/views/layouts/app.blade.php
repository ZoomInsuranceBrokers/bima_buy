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
    <link rel="shortcut icon" href="{{asset('images/favicon.ico')}}" />
    @stack('styles')
    <style>
        .dropdown-left {
            right: 0 !important;
            left: auto !important;
        }
        .quote-item {
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Light shadow */
            padding: 20px;  /* Add some padding inside the quote box */
            border-radius: 8px; /* Rounded corners */
            background-color: #fff; /* White background for each quote */
            margin-bottom: 10px; /* Optional: gives a slight space between quotes */
        }
        .quote-item .form-group {
          margin-bottom: 15px;
        }
    </style>
    <!-- @vite(['resources/js/app.js']) -->
</head>

<body>

    <div class="container-scroller">
        @include('partials.navbar')
        <div class="container-fluid page-body-wrapper">
        
            @switch(Auth::user()->role_id)
                @case('1')
                    @include('partials.admin_sidebar')
                    @break
                @case('2')
                    @include('partials.user_sidebar')
                    @break
                @case('3')
                    @include('partials.zm_sidebar')
                    @break
                @case('4')
                    @include('partials.retail_sidebar')
                    @break
                @default
                    <p>No sidebar available</p>
            @endswitch
            <div class="main-panel">
                @yield('content')
                @include('partials.footer')
            </div>
        </div>
    </div>

    <script src="{{asset('vendors/js/vendor.bundle.base.js')}}"></script>
    <script src="{{asset('vendors/chart.js/Chart.min.js')}}"></script>
    <script src="{{asset('js/jquery.cookie.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/off-canvas.js')}}"></script>
    <script src="{{asset('js/hoverable-collapse.js')}}"></script>
    <script src="{{asset('js/misc.js')}}"></script>
    <script src="{{asset('js/dashboard.js')}}"></script>
    <script src="{{asset('js/todolist.js')}}"></script>
     <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    @stack('scripts')

</body>


</html>