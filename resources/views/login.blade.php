<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in | {{env('APP_NAME')}}</title>
    <link rel="shortcut icon" href="{{asset('favicon.png')}}" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{asset('plugins/fontawesome/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/toastr/toastr.min.css')}}">
    <link rel="stylesheet" href="{{asset('adminlte/css/adminlte.min.css')}}">
</head>

<body class="login-page" style="background-color: #891010;">
    <div class="login-box">
        <div class="card card-outline">
            <div class="card-header text-center" style="background: #891010;">
                <img src="{{asset('wb-logo2.webp')}}" alt="AdminLTE Logo" style="width: 89% !important;">
            </div>
            <div class="card-body">
                <h4 class="text-center text-bold">Login</h4>
                <form action="{{route('login.process')}}" method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" name="email" placeholder="Enter your email." required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fa fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" name="password" placeholder="Enter your password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fa fa-user-lock"></span>
                            </div>
                        </div>
                    </div>
                    <button id="submit_btn" type="submit" class="btn btn-block text-light" style="background-color: #a06b14">Continue
                        <i id="custom_spinner" class="fa fa-spinner fa-spin ml-1 d-none" style="font-size: 13px;"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('adminlte/js/adminlte.js')}}"></script>
    <script src="{{asset('plugins/toastr/toastr.min.js')}}"></script>
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
        };
    </script>
    @php
    if(session()->has('status')){
    $type = session('status');
    $alert_type = $type['alert_type'];
    $msg = $type['message'];
    echo "<script>
        toastr['$alert_type']('$msg');
    </script>";
    }
    @endphp
</body>

</html>