<!doctype html>
<html lang="{{ empty(app()->getLocale()) || app()->getLocale() == 'th' ? 'th' : 'en' }}" dir="ltr">

<head>

    @include('layouts.master.main.head')
    @include('layouts.master.main.style')
    
</head>

<body class="bg-login">
    <!--wrapper-->
    <div class="wrapper">
        <div class="section-authentication-signin d-flex align-items-center justify-content-center my-5 my-lg-0">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </div>
    <!-- vertical-light.scripts -->
    @include('layouts.master.main.script')
</body>

</html>
