<!doctype html>
<html lang="th" class="color-sidebar <?=(!empty(env('SIDEBAR_COLOR')))?env('SIDEBAR_COLOR'):'sidebarcolor6'?>">

<head>
    @include('layouts.master.main.head')

    @include('layouts.master.main.style_tab')
</head>

<body>
    <!--wrapper-->
    <div class="wrapper">
        <!--sidebar wrapper -->
        @include('layouts.master.sidebar-menu')
        {{-- Header --}}
        @include('layouts.master.app-header')
        <!--end sidebar wrapper -->
        <!--start page wrapper -->
        @yield('content')
        <!--end page wrapper --> 
    </div>
    <!--start overlay-->
    <div class="overlay toggle-icon"></div>
    <!--end overlay-->
    <!--Start Back To Top Button-->
    <a href="#" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
    <!--End Back To Top Button-->
    @yield('modals')
    {{-- Switcher footer  --}}
    @include('layouts.master.app-footer')

    {{-- Switcher Theme  --}}
    {{-- @include('layouts.vertical.app-switcher') --}}

    <!-- vertical-light.scripts -->
    @include('layouts.master.main.script_tab')
</body>

</html>
