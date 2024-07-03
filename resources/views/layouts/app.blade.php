<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome/css/all.min.css') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('adminlte/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <title>@yield('title') | {{ env('APP_NAME') }}</title>
    @yield('header-css')
    @yield('header-script')
</head>

<body class="sidebar-mini layout-fixed">
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="" src="{{ asset('wb-logo2.webp') }}" alt="AdminLTELogo" style="width: 20%; height: 10%;">
        {{-- class="animation__shake" --}}
    </div>
    @include('layouts.navbar')
    @include('layouts.sidebar')

    <div class="wrapper">
        @section('main')
        @show
        <footer class="main-footer text-sm">
            <strong>Copyright &copy; {{ date('Y') }} <a href="javascript:void(0);">Wedding Banquets</a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                {{-- <a target="_blank" href="https://www.tricoders.in">Developer Yash</a> --}}
            </div>
        </footer>
    </div>

    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte/js/adminlte.js') }}"></script>
    <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/common.js') }}"></script>
    @php
        if (session()->has('status')) {
            $type = session('status');
            $alert_type = $type['alert_type'];
            $msg = $type['message'];
            echo '<script>
                toastr[`$alert_type`](`$msg`);
            </script>';
        }
    @endphp
    @yield('footer-script')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('meta_title_count').addEventListener('input', function() {
                var textLength = this.value.length;
                document.getElementById('charCountmeta_title_count').innerText = textLength + " Characters";
            });
            document.getElementById('meta_description_count').addEventListener('input', function() {
                var textLength = this.value.length;
                document.getElementById('charCountmeta_description_count').innerText = textLength +
                    " Characters";
            });
        });

        // global function: for http request
        function common_ajax(request_url, method, body = null) {
            return fetch(request_url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                },
                body: body
            })
        }

        function handle_update_phone_no(update_for, data_id) {
            if (update_for == "venue") {
                action_url = `{{ route('venue.update_phoneNumber') }}/${data_id}`;
            } else {
                action_url = `{{ route('vendor.update_phoneNumber') }}/${data_id}`;
            }
            const updatePhoneNoModal = document.getElementById('updatePhoneNoModal');
            const modal = new bootstrap.Modal(updatePhoneNoModal);
            updatePhoneNoModal.querySelector('form').action = action_url;
            updatePhoneNoModal.querySelector('input[name="phone_number"]').value = "";
            modal.show();
        }

        function handle_update_meta(update_for, data_id) {
            if (update_for == "venue") {
                url_for_fetch = `{{ route('venue.fetch_meta') }}/${data_id}`;
                url_for_update = `{{ route('venue.update_meta') }}/${data_id}`;
            } else {
                url_for_fetch = `{{ route('vendor.fetch_meta') }}/${data_id}`;
                url_for_update = `{{ route('vendor.update_meta') }}/${data_id}`;
            }
            fetch(url_for_fetch).then(response => response.json()).then(data => {
                const updateMetaModal = document.getElementById('updateMetaModal');
                const modal = new bootstrap.Modal(updateMetaModal);
                const meta = data.meta;
                modal.show();
                updateMetaModal.querySelector('form').action = url_for_update;
                updateMetaModal.querySelector('input[name=meta_title]').value = meta.meta_title;
                updateMetaModal.querySelector('textarea[name=meta_description]').value = meta.meta_description;
                updateMetaModal.querySelector('textarea[name=meta_keywords]').value = meta.meta_keywords;
            })
        }
    </script>
</body>

</html>
