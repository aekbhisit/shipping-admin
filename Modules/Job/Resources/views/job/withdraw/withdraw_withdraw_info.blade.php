
<div class="body table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th colspan="2"><h5>ข้อมูลการถอนเงิน</h5> </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th width="30%" scope="row">ยอด</th>
                <td><?=$job->amount?></td>
            </tr>
            <tr>
                <th width="30%" scope="row">เข้าบัญขี</th>
                <td>
                    <?php if(!empty($job->to_bank)){ ?>
                    [<?=$job->to_bank->bank_names->code?>] <?=$job->to_bank->acc_name?> <?=$job->to_bank->acc_no?>
                    <?php } ?>  
                    </td>
            </tr>
            <tr>
                <th width="30%" scope="row">จากบัญขี</th>
                <td>
                    <?php if(!empty($job->withdraw_from_bank)){ ?>
                    [<?=$job->withdraw_from_bank->bank_names->code?>] <?=$job->withdraw_from_bank->bank_account?> <?=$job->withdraw_from_bank->bank_number?>
                    <?php } ?>  
                    </td>
            </tr>
            <tr>
                <th width="30%" scope="row">วันที่ เวลา</th>
                <td><?=$job->withdraw_response_at?></td>
            </tr>
            <tr>
                <th width="30%" scope="row" >เครดิตลูกค้า</th>
                <td>
                    <div class="row">
                        <div class="col-md-4"><strong>ก่อนหน้า</strong></div>
                        <div class="col-md-8"><?=$job->balance_bf?></div>
                        <div class="col-md-4"><strong>หลัง</strong></div>
                        <div class="col-md-8"><?=$job->balance_af?></div>
                    </div>
                </td>
            </tr>
            <tr>
                <th width="30%" scope="row">Turn Over</th>
                <td><?=$job->turnover?></td>
            </tr>
            <?php 
                if(!empty($current_promotion)){
            ?>             
            <tr style="background-color:red ; color:#fff;">
                <td colspan="2">
                    <div class="row">
                        <div class="col-md-12" ><strong>ติดโปรโมชั่น </strong> <?=$current_promotion->pro_name?></div>
                        <div class="col-md-12"><strong>Turn Over : </strong> <?=$current_promotion->pro_turnover?></div>
                        <div class="col-md-12"><strong>ยอดโปร: </strong> <?=$job->customer->current_promotion_amount?></div>
                        <div class="col-md-12"><strong>วันที่รับโปร: </strong> <?=$job->customer->current_promotion_date?></div>
                    </div>
                </td>
            </tr>
            <?php 
               }
            ?> 
            
        </tbody>
    </table>
</div>
