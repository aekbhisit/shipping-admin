@extends('layouts.datatable')
@section('styles')
@endsection

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
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.statement.transfer.index') }}">รายการโอนเงิน</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">รายละเอียด</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('admin.statement.transfer.index') }}" role="button" class="btn btn-primary"><i
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
                            <table class="table table-striped-columns border" style="width: 100%">
                                <tr>
                                    <td width="35%" class="text-capitalize">job_id</td>
                                    <td width="65%">{{ !empty($data->job_id) ? $data->job_id : '-' }}</td>
                                </tr>
                                <tr>
                                    <td width="35%" class="text-capitalize">acc_id</td>
                                    <td width="65%">{{ !empty($data->acc_id) ? $data->acc_id : '-' }}</td>
                                </tr>
                                <tr>
                                    <td width="35%" class="text-capitalize">request_body</td>
                                    <td width="65%" class="text-break">
                                        {{ !empty($data->request_body) ? $data->request_body : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td width="35%" class="text-capitalize">request_at</td>
                                    <td width="65%">
                                        {{ !empty($data->request_at) ? date('Y-m-d H:i', strtotime($data->request_at)) : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td width="35%" class="text-capitalize">response_body</td>
                                    <td width="65%">
                                        <p class="mb-1 text-capitalize">status :
                                            {{ !empty($data->response_body['status']) ? $data->response_body['status'] : '-' }}
                                        </p>
                                        <p class="mb-1 text-capitalize">action :
                                            {{ !empty($data->response_body['action']) ? $data->response_body['action'] : '-' }}
                                        </p>
                                        <hr class="my-1">
                                        <p class="mb-1 text-capitalize"><b>Data Status</b></p>
                                        <p class="mb-1 text-capitalize">header :
                                            {{ !empty($data->response_body['data']['status']['header']) ? $data->response_body['data']['status']['header'] : '-' }}
                                        </p>
                                        <p class="mb-1 text-capitalize">code :
                                            {{ !empty($data->response_body['data']['status']['code']) ? $data->response_body['data']['status']['code'] : '-' }}
                                        </p>
                                        <p class="mb-1 text-capitalize">description :
                                            {{ !empty($data->response_body['data']['status']['description']) ? $data->response_body['data']['status']['description'] : '-' }}
                                        </p>
                                        <hr class="my-1">
                                        <p class="mb-1 text-capitalize"><b>Data transaction</b></p>
                                        <div class="card mb-1">
                                            <div class="card-body">
                                                <p class="mb-1 text-capitalize">
                                                    transactionId :
                                                    {{ !empty($data->response_body['data']['data']['transactionId']) ? $data->response_body['data']['data']['transactionId'] : '-' }}
                                                </p>
                                                <p class="mb-1 text-capitalize">transactionDateTime :
                                                    {{ !empty($data->response_body['data']['data']['transactionDateTime']) ? date('Y-m-d H:i', strtotime($data->response_body['data']['data']['transactionDateTime'])) : '-' }}
                                                </p>
                                                <p class="mb-1 text-capitalize">remainingBalance :
                                                    {{ !empty($data->response_body['data']['data']['remainingBalance']) ? $data->response_body['data']['data']['remainingBalance'] : '-' }}
                                                </p>
                                                <p class="mb-1 text-capitalize">status :
                                                    {{ !empty($data->response_body['data']['data']['status']) ? $data->response_body['data']['data']['status'] : '-' }}
                                                </p>
                                                <p class="mb-1 text-capitalize">orgRquid :
                                                    {{ !empty($data->response_body['data']['data']['orgRquid']) ? $data->response_body['data']['data']['orgRquid'] : '-' }}
                                                </p>
                                                <p class="mb-1 text-capitalize">isForcePost :
                                                    {{ !empty($data->response_body['data']['data']['isForcePost']) ? $data->response_body['data']['data']['isForcePost'] : '-' }}
                                                </p>
                                                <p class="mb-1 text-capitalize">remainingPoint :
                                                    {{ !empty($data->response_body['data']['data']['remainingPoint']) ? $data->response_body['data']['data']['remainingPoint'] : '-' }}
                                                </p>
                                            </div>
                                        </div>
                                        @if (!empty($data->response_body['data']['data']['additionalMetaData']['paymentInfo']))
                                            <p class="mb-1 text-capitalize">payment Info</p>
                                            @foreach ($data->response_body['data']['data']['additionalMetaData']['paymentInfo'] as $item)
                                                <div class="card ">
                                                    <div class="card-body d-flex">
                                                        <div class="p-2">
                                                            <img src="{{ !empty($item['type']) && CheckFileInServer($item['type']) ? $item['type'] : URL::asset('assets/images/logo-icon.png') }}"
                                                                class="img-fluid rounded-start" style="width: 160px">
                                                        </div>
                                                        <div class="p-2">
                                                            <p class="mb-1 text-capitalize">type :
                                                                {{ !empty($item['type']) ? $item['type'] : '-' }}
                                                            </p>
                                                            <p class="mb-1 text-capitalize">title :
                                                                {{ !empty($item['title']) ? $item['title'] : '-' }}
                                                            </p>
                                                            <p class="mb-1 text-capitalize">header :
                                                                {{ !empty($item['header']) ? $item['header'] : '-' }}
                                                            </p>
                                                            <p class="mb-1 text-capitalize">description :
                                                                {{ !empty($item['description']) ? $item['description'] : '-' }}
                                                            </p>
                                                            <p class="mb-1 text-capitalize"text-break">QRstring :
                                                                {{ !empty($item['QRstring']) ? $item['QRstring'] : '-' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td width="35%" class="text-capitalize">response_at</td>
                                    <td width="65%">
                                        {{ !empty($data->response_at) ? date('Y-m-d H:i', strtotime($data->response_at)) : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td width="35%" class="text-capitalize">status</td>
                                    <td width="65%">{{ !empty($data->status) ? $data->status : '-' }}</td>
                                </tr>
                                <tr>
                                    <td width="35%" class="text-capitalize">msg</td>
                                    <td width="65%">{{ !empty($data->msg) ? $data->msg : '-' }}</td>
                                </tr>
                                <tr>
                                    <td width="35%" class="text-capitalize">updated_by</td>
                                    <td width="65%">{{ !empty($data->updated_by) ? $data->updated_by : '-' }}</td>
                                </tr>
                                <tr>
                                    <td width="35%" class="text-capitalize">created_at</td>
                                    <td width="65%">
                                        {{ !empty($data->created_at) ? date('Y-m-d H:i', strtotime($data->created_at)) : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td width="35%" class="text-capitalize">updated_at</td>
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

@section('scripts')
    <!-- module js css -->
    <link rel="stylesheet" href="{{ mix('css/default.css') }}">
    <script src="{{ mix('js/default.js') }}"></script>
@endsection
