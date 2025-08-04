@extends('layouts.datatable')
@section('styles')
@endsection

@section('content')
@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Statement</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
<a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.statement.temp.index') }}">Temp
                                    Statement</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">รายละเอียด</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('admin.statement.temp.index') }}" role="button" class="btn btn-primary"><i
                                class="fadeIn animated bx bx-arrow-back"></i>
                            กลับ
                        </a>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card">
                        <div class="card-body">
                            {{-- @dump($data) --}}
                            <table class="table table-striped-columns border">
                                <tr>
                                    <td class="text-capitalize" width="35%">acc_id</td>
                                    <td width="65%">{{ !empty($data->acc_id) ? $data->acc_id : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">source_from</td>
                                    <td width="65%">{{ !empty($data->source_from) ? $data->source_from : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">hash</td>
                                    <td width="65%">{{ !empty($data->hash) ? $data->hash : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">detail</td>
                                    <td width="65%">
                                        <p class="mb-1 text-capitalize">sender :
                                            {{ !empty($data->detail['sender']) ? $data->detail['sender'] : '-' }}
                                        </p>
                                        <p class="mb-1 text-capitalize">message :
                                            {{ !empty($data->detail['message']) ? $data->detail['message'] : '-' }}
                                        </p>
                                        <p class="mb-1 text-capitalize">date_time :
                                            {{ !empty($data->detail['date_time']) ? date('Y-m-d H:i', strtotime($data->detail['date_time'])) : '-' }}

                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">status</td>
                                    <td width="65%">{{ !empty($data->status) ? $data->status : '-' }}</td>
                                </tr>

                                <tr>
                                    <td class="text-capitalize" width="35%">created_at</td>
                                    <td width="65%">
                                        {{ !empty($data->created_at) ? date('Y-m-d H:i', strtotime($data->created_at)) : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">updated_at</td>
                                    <td width="65%">
                                        {{ !empty($data->updated_at) ? date('Y-m-d H:i', strtotime($data->updated_at)) : '-' }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!--end page wrapper -->
@endsection
@endsection

@section('scripts')
<!-- module js css -->
<link rel="stylesheet" href="{{ mix('css/default.css') }}">
<script src="{{ mix('js/default.js') }}"></script>
@endsection
