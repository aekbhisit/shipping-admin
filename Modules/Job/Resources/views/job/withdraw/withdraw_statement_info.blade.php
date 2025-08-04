<?php
    $found_statement_color = '#e17e7e;' ;
    if(!empty($job->statement_id)){
        $found_statement_color = '#89c07e;' ;
    }

?>
<div class="body table-responsive">
    <input type="hidden" name="job_id" id="job_id" value="<?=$job->id?>">
    <table class="table">
        <thead>
            <tr>
                <th colspan="2"><h5>ข้อมูลจาก statement</h5></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th width="30%" scope="row">ยอด</th>
                <td><?=(!empty($job->statement_id))?$statements[0]->bank_web->report_value:''?></td>
            </tr>
            <tr>
                <th width="30%" scope="row">ข้อมูลจาก statement</th>
                <td><?=(!empty($job->statement_id))?$statements[0]->bank_web->report_detail:''?></td>
            </tr>
            <tr>
                <th width="30%" scope="row">โอนเงินจาก</th>
                <td>
                    <?php if(!empty($job->from_bank)){ ?>
                    [<?=$job->from_bank->bank_names->code?>] <?=$job->from_bank->acc_name?> <?=$job->from_bank->acc_no?>
                    <?php } ?>
                    </td>
            </tr>
           
            <tr>
                <th width="30%" scope="row">วันที่ เวลา</th>
                <td><?=(!empty($job->statement_id))?$statements[0]->report_datetime:''?></td>
            </tr>
            <tr>
                <th width="30%" scope="row">ยอดเงินธนาคาร</th>
                <td>
                    <div class="row">
                        <div class="col-md-4"><strong>ก่อนโอน</strong></div>
                        <div class="col-md-8"><?=(!empty($job->withdraw_balance_bf))?number_format($job->withdraw_balance_bf,2):''?>&nbsp;</div>
                        <div class="col-md-4"><strong>หลังโอน</strong></div>
                        <div class="col-md-8"><?=(!empty($job->withdraw_balance_af))?number_format($job->withdraw_balance_af,2):''?>&nbsp;</div>
                    </div>
                </td>
            </tr>
            <tr style="background-color:<?=$found_statement_color?>" >
                <th width="30%" scope="row">สถานะการโอน</th>
                <td>
                    <?php if(!empty($job->withdraw_response)){ 
                        $wd_resp = json_decode($job->withdraw_response,1);
                    ?>
                    <div class="row">
                        <div class="col-md-3"><strong>สถานะ</strong></div><div class="col-md-9">สำเร็จ</div>
                        <div class="col-md-3"><strong>เลขอ้างอิง</strong></div><div class="col-md-9"><?=(isset($wd_resp['data']['data']['transactionId']))?$wd_resp['data']['data']['transactionId']:''?></div>
                        <div class="col-md-3"><strong>วันที่เวลา</strong></div><div class="col-md-9"><?=(isset($wd_resp['data']['data']['transactionDateTime']))?$wd_resp['data']['data']['transactionDateTime']:''?></div>
                        <div class="col-md-3"><strong>ยอดคงเหลือ</strong></div><div class="col-md-9"><?=(isset($wd_resp['data']['data']['remainingBalance']))?number_format($wd_resp['data']['data']['remainingBalance'],2):''?></div>
                    </div>
                    <?php }else{ 
                        echo 'ยังไม่ได้โอนเงิน';
                    } ?>
                </td>
            </tr>
        </tbody>
    </table>

    <?php if($allow_edit) { ?>
    <?php if($job->status['value']<8){ ?>
    <div class="col-12">
        <div class="card bg-success">
            <div class="card-body text-white">
                <h5 class="card-title text-white">เลือกธนาคารถอนเงิน</h5>
                <div class="row g-3">
                    <div class="col-12">
                        <select class="form-control" id="bank_id" name="bank_id">
                            <option value="0"> -- เลือกธนาคาร -- </option>
                            <?php 
                            foreach($bank as $b){ 
                            ?>
                                <option value="<?=$b->id?>"> <?=$b->bank_account?> *<?=substr($b->bank_number, -4);?> (คงเหลือ : <?=$b->bank_amount?>) </option>
                            <?php 
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary m-t-15 waves-effect"  onclick="setConfirmTransferWithdraw(<?=$job->status['value']?>);"><i class="lni lni-save"></i> ถอนเงิน </button>
                    </div>
                    <div class="col-auto">
                        <button type="button" style="margin-left: 10px;" class="btn btn-warning m-t-15 waves-effect"  onclick="setConfirmComplete(<?=$job->id?>);"><i class="lni lni-checkmark"></i> ใบงานนี้ทำเสร็จแล้ว</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    <?php } ?>

    <?php if($allow_edit) { ?>
    <?php if($job->status['value']==7){ ?>
    <div class="col-12">
        <div class="card bg-warning">
            <div class="card-body text-white">
                <h5 class="card-title text-white">ยืนยันถอนเงินเสร็จ</h5>
                <div class="row g-3">
                    <button type="button" class="btn btn-primary m-t-15 waves-effect" onclick="setConfirmWithdraw(<?=$job->status['value']?>)">ยืนยันถอนเงินเสร็จ</button>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    <?php } ?>

    <?php if($job->status['value']<=8){ ?>
    <div class="col-12">
        <div class="card bg-dark">
            <div class="card-body text-white">
                <h5 class="card-title text-white">ยกเลิก 
                    <?php if($job->status['value']==8){ ?>
                        <span class="text-white fs-6">ยกเลิกแล้ว เมี่อ <?=$job->cancel_at?></span>
                    <?php } ?>
                </h5>

                <div class="row g-3">
                    <div class="col-12">
                        <input type="text" name="cancel_note" id="cancel_note" class="form-control" placeholder="เหตุผลที่ยกเลิก" value="<?=$job->cancel_note?>" <?=($job->status['value']>=8||!$allow_edit)?'readonly':''?> >
                    </div>
                    <div class="col-6" <?=($job->status['value']>=8||!$allow_edit)?'style="display:none;"':''?> >
                        <button type="button" class="btn btn-warning m-t-15 waves-effect" onclick="setCancelWithdraw(<?=$job->status['value']?>)">ยกเลิก คืนเคดดิต</button>
                    </div>
                    <div class="col-6" <?=($job->status['value']>=8||!$allow_edit)?'style="display:none;"':''?> >
                        <button type="button" style="margin-left: 10px;" class="btn btn-secondary m-t-15 waves-effect" onclick="setCancelWithdraw(<?=$job->status['value']?>,0)">ยกเลิก ไม่คืนเครดิต</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

</div>
