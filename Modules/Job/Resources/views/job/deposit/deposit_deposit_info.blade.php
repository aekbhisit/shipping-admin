
<div class="body table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th colspan="2"><h5>ข้อมูลการฝากเงิน</h5></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th width="30%" scope="row">ยอด</th>
                <td><?=$job->amount?></td>
            </tr>
            <tr>
                <th width="30%" scope="row">จากบัญขี</th>
                <td>
                    <?php if(!empty($job->from_bank)){ ?>
                    [<?=$job->from_bank->bank_names->code?>] <?=$job->from_bank->acc_name?> <?=$job->from_bank->acc_no?>
                    <?php } ?>    
                    </td>
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
                <th width="30%" scope="row">วันที่ เวลา</th>
                <td><?=$job->transfer_datetime?></td>
            </tr>
            <tr>
                <th colspan="2" scope="row">สลิป</th>
            </tr>
             <tr>
                <td colspan="2"><img src="<?=Storage::url($job->transfer_slip)?>" style="max-width: 100% ; max-height: 300px; position: relative;" ></td>
            </tr>
            
        </tbody>
    </table>
</div>
