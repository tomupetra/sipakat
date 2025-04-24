<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="{{ asset('admin/css/styles.css') }}" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/pell/dist/pell.min.css">
    <script src="https://unpkg.com/pell"></script>
    <link rel="icon" href="{{ asset('assets/img/logo.png') }}" type="image/png">

</head>

<body class="sb-nav-fixed">

    @include('layouts.partials.navbar')

    <div id="layoutSidenav">

        <div id="layoutSidenav_nav">
            @include('layouts.partials.sidebar')
        </div>

        <div id="layoutSidenav_content">

            <main>
                <div class="container-fluid px-4">
                    @if (session('success'))
                        <div class="alert alert-success mt-3" id="success-alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger mt-3" id="error-alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    @yield('content')
                </div>
            </main>

            @include('layouts.partials.footer')


        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                let successAlert = document.getElementById('success-alert');
                if (successAlert) {
                    successAlert.style.display = 'none';
                }
                let errorAlert = document.getElementById('error-alert');
                if (errorAlert) {
                    errorAlert.style.display = 'none';
                }
            }, 3000); // 3 seconds
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
    <script src="{{ asset('admin/js/scripts.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
        crossorigin="anonymous"></script>
    <script src="{{ asset('admin/js/datatables-simple-demo.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        var editor = pell.init({
            element: document.getElementById('editor'),
            onChange: function(html) {
                document.querySelector('#content').value = html;
            },
            actions: ['bold', 'italic', 'underline', 'olist', 'ulist', 'link', 'image']
        });
    </script>



</body>

</html>
