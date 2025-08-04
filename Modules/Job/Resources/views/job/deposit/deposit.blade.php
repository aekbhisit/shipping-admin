@extends('layouts.form')
@section('styles')
@endsection


@section('content')
    <?php
    $show_bank = '';
    if ($job->type['value'] == 1) {
        if (!empty($job->to_bank)) {
            $show_bank_acc_no = ' *' . substr($job->to_bank->acc_no, -4);
            [$acc_fname, $acc_last_name] = explode(' ', $job->to_bank->acc_name);
    
            $show_bank_acc_name = mb_substr($acc_fname, 0, 5) . '* ' . mb_substr($acc_last_name, 0, 5) . '*';
    
            $show_bank = '[' . $job->to_bank->bank_names->code . ']' . $show_bank_acc_no . ' ' . $show_bank_acc_name;
        }
    }
    ?>
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">ใบงาน</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">ฝาก</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    @if (roles('admin.job.job.unlock'))
                        <?php if($allow_edit) { ?>
                        <?php if($job->status['value']<8){ ?>
                        <a href="/admin/job/unlock/<?= $job->id ?>/0">
                            <button type="button" class="btn btn-info m-t-15 waves-effect"> <i class="lni lni-unlock"></i>
                                ปลดล็อก</button>
                        </a>
                        <?php } ?>
                        <?php } ?>
                    @endif
                    @if (roles('admin.job.job.set_confirm_complete'))
                        <?php if($allow_edit) { ?>
                        <?php if($job->status['value']<8){ ?>
                        <button type="button" style="margin-left: 10px;" class="btn btn-warning m-t-15 waves-effect"
                            onclick="setConfirmComplete(<?= $job->id ?>);"> <i class="lni lni-checkmark"></i>
                            ใบงานนี้ทำเสร็จแล้ว</button>
                        <?php } ?>
                        <?php } ?>
                    @endif

                    <div class="btn-group">
                        <a href="{{ route('admin.job.job.index') }}">
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
                    {{-- <div class="card border-top border-0 border-4 border-primary"> --}}
                    {{-- <div class="card-body p-5"> --}}
                    <div class="row">
                        <dev class="col-md-6">
                            <div class="card border-top border-0 border-4 border-success">
                                <div class="card-body p-3">
                                    <!-- card customer info -->
                                    @includeIf('job::job.deposit.deposit_cust_info')
                                    <!-- .card customer info -->
                                </div>
                            </div>

                            <div class="card border-top border-0 border-4 border-success">
                                <div class="card-body p-3">
                                    <!-- card deposit info -->
                                    @includeIf('job::job.deposit.deposit_deposit_info')
                                    <!-- .card deposit info -->
                                </div>
                            </div>

                        </dev>
                        <dev class="col-md-6">
                            <div class="card border-top border-0 border-4 border-success">
                                <div class="card-body p-3">
                                    <!-- card slip info -->
                                    @includeIf('job::job.deposit.deposit_confirm_info')
                                    <!-- .card slip info -->
                                </div>
                            </div>
                            <div class="card border-top border-0 border-4 border-success">
                                <div class="card-body p-3">
                                    <!-- card statement info -->
                                    @includeIf('job::job.deposit.deposit_statement_info')
                                    <!-- .card statement info -->
                                </div>
                            </div>
                        </dev>
                        <hr>
                        <dev class="col-md-12">
                            <div class="card border-top border-0 border-4 border-success">
                                <div class="card-body p-3">
                                    <!-- card statement history -->
                                    @includeIf('job::job.deposit.deposit_statement_history')
                                    <!-- .card statement history -->
                                </div>
                            </div>

                        </dev>

                    </div>
                    {{-- </div> --}}
                    {{-- </div> --}}

                </div>
            </div>
            <!--end row-->
        </div>
    </div>
    <!--end page wrapper -->
@endsection('content')

@section('scripts')
    <!-- module js css -->
    <link rel="stylesheet" href="{{ mix('css/job.css') }}">
    <script src="{{ mix('js/job.js') }}?<?= time() ?>"></script>
    <script src="{{ mix('js/deposit.js') }}?<?= time() ?>"></script>
@endsection
