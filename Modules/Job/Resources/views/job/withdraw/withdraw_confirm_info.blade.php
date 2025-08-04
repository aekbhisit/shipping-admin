<div class="body table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th colspan="3"><h5>ข้อมูลการยืนยัน</h5></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th width="30%" scope="row">สร้างโดย</th>
                <td><?=(!empty($job->created_user))?$job->created_user->first_name:''?></td>
                <td width="30%"><?=$job->created_at?></td>
            </tr>
            <tr>
                <th width="30%" scope="row">แก้ไข</th>
                <td><?=(!empty($job->created_user))?$job->updated_user->first_name:''?></td>
                <td width="30%"><?=$job->updated_at?></td>
            </tr>
            <tr>
                <th width="30%" scope="row">ล็อกโดย</th>
                <td><?=(!empty($job->locked_user))?$job->locked_user->first_name:''?></td>
                <td width="30%"><?=$job->locked_at?></td>
            </tr>
            <?php if($job->status['value']==9){ ?>
            <tr>
                <th width="30%" scope="row">ยืนยันโดย</th>
                <td><?=(!empty($job->approved_user))?$job->approved_user->first_name:''?></td>
                <td width="30%"><?=$job->approved_at?></td>
            </tr>
            <tr>
                <th width="30%" scope="row">โอนเงินโดย</th>
                <td><?=(!empty($job->banker_user))?$job->banker_user->first_name:''?></td>
                <td width="30%"><?=$job->banker_at?></td>
            </tr>
            <?php } ?>

            <?php if($job->status['value']==8){ ?>
            <tr>
                <th width="30%" scope="row">ยกเลิกโดย</th>
                <td><?=(!empty($job->cancel_user))?$job->cancel_user->first_name:''?></td>
                <td width="30%"><?=$job->cancel_at?></td>
            </tr>
            <?php } ?>

            <?php if($job->refund_by){ ?>
            <tr>
                <th width="30%" scope="row">คืนเงินเครดิตโดย</th>
                <td><?=(!empty($job->refund_user))?$job->refund_user->first_name:''?></td>
                <td width="30%"><?=$job->refund_at?></td>
            </tr>
            <?php } ?>
            
        </tbody>
    </table>
</div>
