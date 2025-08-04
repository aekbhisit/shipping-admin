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
                        <a href="{{ route('admin.user.role.index') }}">
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
                                <h5 class="mb-0 text-primary">Role</h5>
                            </div>
                            <hr>
                            <form class="row g-3" id="role_frm" name="role_frm" method="POST"
                                onsubmit="setSave(); return false;" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" id="role_id" value="{{ !empty($data->id) ? $data->id : '0' }}">
                                <div class="col-md-12">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        placeholder="Name" value="{{ !empty($data->name) ? $data->name : '' }}">
                                </div>
                                <div>
                                    <!-- user::role -->
                                    @includeIf('user::role.role')
                                    <!-- .user::role -->
                                </div>
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-info px-5"><i class="lni lni-save"></i>
                                        Save</button>
                                    <a href="{{ route('admin.user.role.index') }}">
                                        <button type="button" class="btn btn-warning px-5"><i class="lni lni-close"></i>
                                            Cancel</button>
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
    <link rel="stylesheet" href="{{ mix('css/user_role.css') }}">
    <script src="{{ mix('js/user_role.js') }}"></script>
@endsection
