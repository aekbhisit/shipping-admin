<?php
    $found_statement_color = '#e17e7e;' ;
    if(!empty($job->statement_id)){
        $found_statement_color = '#89c07e;' ;
    }

?>
<div class="body table-responsive">
<form id="job_deposit_frm" name="job_deposit_frm" onsubmit="setConfirmDeposit(<?=$job->status['value']?>);return false ;" enctype="multipart/form-data" method="POST" >
    <table class="table">
        <thead>
            <tr>
                <th colspan="2"><h5>ข้อมูลจาก statement</h5></th>
            </tr>
        </thead>
        <tbody>
            
            <tr>
                <th width="40%" scope="row">ยอด</th>
                <td><?=(!empty($job->statement_id))?$statements[0]->bank_web->report_value:''?></td>
            </tr>
            <tr>
                <th scope="row">โอนเงินเข้า</th>
                <td>
                    <?php if($job->to_bank){ ?>
                    [<?=$job->to_bank->bank_names->code?>] <?=$job->to_bank->acc_name?> <?=$job->to_bank->acc_no?>
                    <?php } ?>
                    </td>
            </tr>
            <tr>
                <th scope="row">ข้อมูลจาก statement</th>
                <td><?=(!empty($job->statement_id))?$statements[0]->report_detail:''?></td>
            </tr>
            <tr>
                <th scope="row">วันที่ เวลา</th>
                <td><?=(!empty($job->statement_id))?$statements[0]->report_datetime:''?></td>
            </tr>
           
            <tr style="background-color:<?=$found_statement_color?>">
                <th scope="row">พบรายการโอนเงิน</th>
                <td>
                    <?php 
                    if(!$statements->isEmpty()){
                    ?>
                        <?php foreach($statements as $stm){ 
                            $acc_fn = '';
                            $acc_ln = '';
                            if(!empty($stm->bank_web)){
                                list($acc_fn,$acc_ln) = explode(' ',$stm->bank_web->bank_account);
                            }
                            $checked = (!empty($job->statement_id)&&$stm->id==$job->statement_id)?'checked="checked"':'';
                            $disabled = (!$allow_edit||$job->status['value']>=8)?'disabled':'';
                        ?>
                            <div class="form-check">
                              <input name="statement_id" id="statement_id_<?=$stm->id?>" type="checkbox" value="<?=$stm->id?>" <?=$checked?> <?=$disabled?> >
                              <label class="" for="statement_id_<?=$stm->id?>">
                                [<?=$stm->bank_web->bank_names->code?>]
                                <?=' '.$stm->report_datetime?>
                                <?=' ยอด:'.$stm->report_value?>
                                <?=' บัญชี:'.$acc_fn?>
                                <?=' '.$stm->bank_web->bank_number?>
                              </label>
                            </div>
                        <?php } ?>
                        <?php 
                    }else{
                            echo 'ไม่พบรายการ statement';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th scope="row">เครดิตก่อนหน้า</th>
                <td><?=$job->balance_bf?></td>
            </tr>
            <tr>
                <th scope="row">เครดิตหลัง</th>
                <td><?=$job->balance_af?></td>
            </tr>

            <?php if($allow_edit) { ?>
                <?php if(!$job->is_auto) { ?>    
                    <tr>
                        <th scope="row">สลิป</th>
                        <td>
                            <div class="form-group">
                                <input type="file" name="deposit_slip" id="deposit_slip" class="form-control-file">
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>

            
        </tbody>
    </table>

    <?php if($allow_edit) { ?>
    <?php if($job->status['value']<8){ ?>
    <div class="col-12">
        <div class="card bg-success">
            <div class="card-body text-white">
                <h5 class="card-title text-white">ยืนยัน</h5>
                <div class="row g-3">
                    <input type="hidden" name="job_id" id="job_id" value="<?=$job->id?>">
                    <div class="row g-3">
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary m-t-15 waves-effect"  onclick="setConfirmDeposit(<?=$job->status['value']?>);"><i class="lni lni-save"></i> ยืนยันเติมเงิน และ เสร็จสิ้น</button>
                        </div>
                        <div class="col-auto">
                            <button type="button" style="margin-left: 10px;" class="btn btn-warning m-t-15 waves-effect"  onclick="setConfirmComplete(<?=$job->id?>);"><i class="lni lni-checkmark"></i> ใบงานนี้ทำเสร็จแล้ว</button>
                        </div>
                     </div>
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
                    <div class=" <?=($job->status['value']>=8||!$allow_edit)?'col-12':'col-8'?> ">
                        <input type="text" name="cancel_note" id="cancel_note" class="form-control" placeholder="เหตุผลที่ยกเลิก" value="<?=$job->cancel_note?>" <?=($job->status['value']>=8||!$allow_edit)?'readonly':''?> >
                    </div>
                    <div class="col-4" <?=($job->status['value']>=8||!$allow_edit)?'style="display:none;"':''?> >
                        <a href="javascript:void();" onclick="setCancelDeposit(<?=$job->status['value']?>)" class="btn bg-white text-dark"> <i class="lni lni-cross-circle"></i> ยกเลิก</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

</form>
</div>
