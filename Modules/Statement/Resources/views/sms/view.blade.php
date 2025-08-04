@extends('layouts.form')
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
                            <li class="breadcrumb-item"><a href="{{ route('admin.statement.sms.index') }}">SMS</a></li>
                            <li class="breadcrumb-item active" aria-current="page">รายละเอียด</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('admin.statement.sms.index') }}" role="button" class="btn btn-primary"><i
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
                            <table class="table table-striped-columns border">
                                <tr>
                                    <td class="text-capitalize" width="35%">statement_id</td>
                                    <td width="65%">{{ !empty($data->statement_id) ? $data->statement_id : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">job_id</td>
                                    <td width="65%">{{ !empty($data->job_id) ? $data->job_id : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">phone</td>
                                    <td width="65%">{{ !empty($data->phone) ? $data->phone : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">port</td>
                                    <td width="65%">{{ !empty($data->port) ? $data->port : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">message</td>
                                    <td width="65%">{{ !empty($data->message) ? $data->message : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">bank_no</td>
                                    <td width="65%">{{ !empty($data->bank_no) ? $data->bank_no : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">amount</td>
                                    <td width="65%">{{ !empty($data->amount) ? $data->amount : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">balance</td>
                                    <td width="65%">{{ !empty($data->balance) ? $data->balance : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">time</td>
                                    <td width="65%">
                                        {{ !empty($data->time) ? date('H:i', strtotime($data->time)) : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">date</td>
                                    <td width="65%">
                                        {{ !empty($data->date) ? date('Y-m-d', strtotime($data->date)) : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">date_time</td>
                                    <td width="65%">
                                        {{ !empty($data->date_time) ? date('Y-m-d H:i', strtotime($data->date_time)) : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">sms_time</td>
                                    <td width="65%">
                                        {{ !empty($data->sms_time) ? date('Y-m-d H:i', strtotime($data->sms_time)) : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">bank_id</td>
                                    <td width="65%">{{ !empty($data->bank_id) ? $data->bank_id : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">acc_id</td>
                                    <td width="65%">{{ !empty($data->acc_id) ? $data->acc_id : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">bank_account</td>
                                    <td width="65%">{{ !empty($data->bank_account) ? $data->bank_account : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">bank_number</td>
                                    <td width="65%">{{ !empty($data->bank_number) ? $data->bank_number : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">status</td>
                                    <td width="65%">{{ !empty($data->status) ? $data->status : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">created_by</td>
                                    <td width="65%">{{ !empty($data->created_by) ? $data->created_by : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">updated_by</td>
                                    <td width="65%">{{ !empty($data->updated_by) ? $data->updated_by : '-' }}</td>
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
                                <tr>
                                    <td class="text-capitalize" width="35%">data_member_job</td>
                                    <td width="65%">{{ !empty($data->data_member_job) ? $data->data_member_job : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">data_job</td>
                                    <td width="65%">{{ !empty($data->data_job) ? $data->data_job : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">job_created_status</td>
                                    <td width="65%">
                                        {{ !empty($data->job_created_status) ? $data->job_created_status : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">job_created_at</td>
                                    <td width="65%">
                                        {{ !empty($data->job_created_at) ? date('Y-m-d H:i', strtotime($data->job_created_at)) : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">job_response</td>
                                    <td width="65%">{{ !empty($data->job_response) ? $data->job_response : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">otp</td>
                                    <td width="65%">{{ !empty($data->otp) ? $data->otp : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">otp_ref</td>
                                    <td width="65%">{{ !empty($data->otp_ref) ? $data->otp_ref : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">otp_value</td>
                                    <td width="65%">{{ !empty($data->otp_value) ? $data->otp_value : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-capitalize" width="35%">otp_use</td>
                                    <td width="65%">{{ !empty($data->otp_use) ? $data->otp_use : '-' }}</td>
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
