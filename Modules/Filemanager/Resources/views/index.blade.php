@extends('layouts.app')
@section('styles')
   
@endsection

@section('content')
        
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Tables</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
<a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Data Table</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    {{-- <div class="btn-group">
                        <button type="button" class="btn btn-info"><i class="lni lni-plus me-1"></i> Add</button>
                    </div> --}}
                </div>
            </div>
            <!--end breadcrumb-->
            <h6 class="mb-0 text-uppercase">File manager</h6>
            <hr/>
            <div class="card">
                <div class="card-body">
                    <div id="elfinder"></div>
                </div>
            </div>
            
        </div>
    </div>
    <!--end page wrapper -->

@endsection

@section('scripts')
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
    {{-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script> --}}
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>

    <!-- elFinder CSS (REQUIRED) -->
    <link rel="stylesheet" type="text/css" href="<?= asset($dir.'/css/elfinder.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset($dir.'/css/theme.css') ?>">

    <!-- elFinder JS (REQUIRED) -->
    <script src="<?= asset($dir.'/js/elfinder.min.js') ?>"></script>

    <?php if($locale){ ?>
    <!-- elFinder translation (OPTIONAL) -->
    <script src="<?= asset($dir."/js/i18n/elfinder.$locale.js") ?>"></script>
    <?php } ?>


    <!-- module js css -->
    <link rel="stylesheet" href="{{ mix('css/default.css') }}">
    <script src="{{ mix('js/default.js') }}"></script>
    
    <!-- elFinder initialization (REQUIRED) -->
    <script type="text/javascript" charset="utf-8">
        // Documentation for client options:
        // https://github.com/Studio-42/elFinder/wiki/Client-configuration-options
        $().ready(function() {
            $('#elfinder').elfinder({
                // set your elFinder options here
                <?php if($locale){ ?>
                    lang: '<?= $locale ?>', // locale
                <?php } ?>
                customData: { 
                    _token: '<?= csrf_token() ?>'
                },
                url : '<?=route("elfinder.connector")?>',  // connector URL
                soundPath: '<?=asset($dir."/sounds") ?>',
                height: 500
            });
        });
    </script>
   
@endsection
