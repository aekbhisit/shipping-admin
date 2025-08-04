<div class="body table-responsive">
    <h4>ข้อมูล ฝาก-ถอน</h4>
    <table class="table">
        <thead>
            <tr>
                <th>ประเภท</th>
                <th>ใบงาน</th>
                <th>User</th>
                <th>ธนาคาร</th>
                <th>ยอด</th>
                <th>โปรโมชั่น</th>
                <th>สถานะ</th>
                <th>โดย</th>
                <th>วันที่</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if(!empty($jobs)){
                foreach($jobs as $job){ 
            ?>
                <tr class="<?=$job->type['class']?> <?=$job->status['class']?> ">
                    <td class="type" ><?=$job->type['text']?></td>
                    <td class="code"><?=$job->code?></td>
                    <td>
                        <a href="/customers/customers/<?=$job->cust_id?>" target="_blank">
                        <?php 
                            if(!empty($job->customer_user->username)){
                                echo $job->customer_user->username ;
                            }
                        ?>
                        </a> 
                    </td>
                    <td><?=$show_bank?></td>
                    <td><?=$job->amount?></td>
                    <td><?=$job->promotion_amount?></td>
                    <td class="status"><?=$job->status['text']?></td>
                    <td><?=(!empty($job->created_user))?$job->created_user->first_name:''?></td>
                    <td><?=$job->created_at?></td>
                </tr>
            <?php 
                }
            } 
            ?>
        </tbody>
    </table>
</div>
