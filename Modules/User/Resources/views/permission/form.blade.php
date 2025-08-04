@extends('layouts.form')

@section('styles')
    
@endsection

@section('content')

<!--start page wrapper -->
<div class="page-wrapper">
    <div class="page-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">User</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item">
<a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page">User</li>
                        <li class="breadcrumb-item active" aria-current="page">Add</li>
                    </ol>
                </nav>
            </div>
            <div class="ms-auto">
                <div class="btn-group">
                    <a href="{{ route('admin.user.permission.index') }}">
                        <button type="button" class="btn btn-primary"><i class="fadeIn animated bx bx-arrow-back"></i> กลับ</button>
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
                            <h5 class="mb-0 text-primary">Permission</h5>
                        </div>
                        <hr>
                       <form class="row g-3" id="permission_frm" name="permission_frm" method="POST" onsubmit="setSave(); return false;" enctype="multipart/form-data">
                            @csrf
                           <input type="hidden" name="id" value="{{ !empty($data->id) ? $data->id : '0' }}">
                            {{-- field --}}
                            <div class="col-md-12">
                            <label class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Name"
                                        value="{{ !empty($data->name) ? $data->name : '' }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">{{__('user::permission.field.group')}}</label>
                                <input type="text" class="form-control" id="group" name="group" readonly
                                    value="{{ !empty($data->group) ? $data->group : '' }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">{{__('user::permission.field.module')}}</label>
                                <input type="text" class="form-control" id="module" name="module" readonly
                                    value="{{ !empty($data->module) ? $data->module : '' }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">{{__('user::permission.field.page')}}</label>
                                <input type="text" class="form-control" id="page" name="page" readonly
                                    value="{{ !empty($data->page) ? $data->page : '' }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">{{__('user::permission.field.action')}}</label>
                                <input type="text" class="form-control" id="action" name="action" readonly
                                    value="{{ !empty($data->action) ? $data->action : '' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{__('user::permission.field.route_name')}}</label>
                                <input type="text" class="form-control" id="route_name" name="route_name" readonly
                                    value="{{ !empty($data->route_name) ? $data->route_name : '' }}">
                            </div>
                           
                            {{-- .field --}}
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-info px-5"><i class="lni lni-save"></i> Save</button>
                                <a href="{{ route('admin.user.permission.add') }}">
                                    <button type="buttion" class="btn btn-warning px-5"><i class="lni lni-close"></i> Cancel</button>
                                </a>
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
    <link rel="stylesheet" href="{{ mix('css/user_permission.css') }}">
    <script src="{{ mix('js/user_permission.js') }}"></script>
@endsection
