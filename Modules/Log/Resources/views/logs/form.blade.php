@extends('layouts.form')
@section('styles')
@endsection


@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Forms</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Form Elements</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('admin.log.log.index') }}">
                            <button type="button" class="btn btn-primary"><i class="fadeIn animated bx bx-arrow-back"></i>
                                กลับ</button>
                        </a>
                    </div>
                </div>
            </div>
            <!--end breadcrumb-->
            <div class="row">
                <div class="col-xl-12 mx-auto">
                    {{-- <h6 class="mb-0 text-uppercase">Basic Form</h6>
                <hr/> --}}
                    <div class="card border-top border-0 border-4 border-primary">
                        <div class="card-body p-5">
                            <div class="card-title d-flex align-items-center">
                                <div><i class="bx bxs-user me-1 font-22 text-primary"></i>
                                </div>
                                <h5 class="mb-0 text-primary">User Registration</h5>
                            </div>
                            <hr>

                            <form class="row g-3" id="permission_frm" name="permission_frm" method="POST"
                                onsubmit="setSave(); return false;" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" value="{{ !empty($data->id) ? $data->id : '0' }}">
                                <div class="card">
                                    <div class="card-header p-3 border-bottom">
                                        <div class="tabs-menu1 ">
                                            <!-- Tabs -->
                                            <ul class="nav panel-tabs" id="permission-main-tab">
                                                <li><a href="#permission-main-tab-1" class="active"
                                                        data-toggle="tab">{{ __('log::module.name') }}</a></li>
                                            </ul>
                                            <!-- .Tabs -->
                                        </div>
                                    </div>
                                    <div class="card-body ">
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="permission-main-tab-1">

                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <h5 class="card-title">Raw <?= $data->log_name ?></h5>
                                                                    {{-- <h6 class="card-subtitle mb-2 text-muted"><?= $data->log_name ?></h6> --}}
                                                                    <p class="card-text">
                                                                        <pre><?php print_r($data->toArray()); ?></pre>
                                                                    </p>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <?php
                                                            $properties = !empty($data->properties) ? json_decode($data->properties, 1) : '';
                                                            ?>
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <h5 class="card-title">New</h5>
                                                                    {{-- <h6 class="card-subtitle mb-2 text-muted"><?= $data->log_name ?></h6> --}}
                                                                    <p class="card-text">
                                                                        <pre>
                                                                        <?php
                                                                        if (!empty($properties['attributes'])) {
                                                                            print_r($properties['attributes']);
                                                                        }
                                                                        ?> 
                                                                    </pre>
                                                                    </p>

                                                                </div>
                                                            </div>
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <h5 class="card-title">Old</h5>
                                                                    {{-- <h6 class="card-subtitle mb-2 text-muted"><?= $data->log_name ?></h6> --}}
                                                                    <p class="card-text">
                                                                        <pre>
                                                                        <?php
                                                                        if (!empty($properties['old'])) {
                                                                            print_r($properties['old']);
                                                                        }
                                                                        ?> 
                                                                    </pre>
                                                                    </p>

                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>

                </div>
            </div>
            <!--end row-->
        </div>
    </div>
    <!--end page wrapper -->
@endsection('content')

@section('scripts')
    <!-- module js css -->
    <link rel="stylesheet" href="{{ mix('css/default.css') }}">
    <script src="{{ mix('js/default.js') }}"></script>
@endsection
