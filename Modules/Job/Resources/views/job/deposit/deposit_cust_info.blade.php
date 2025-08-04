
<div class="body table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th colspan="2"><h5>ข้อมูลลูกค้า</h5></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th width="30%" scope="row">ชื่อ</th>
                <td><a href="/customers/customers/<?=$job->customer->id?>" target="_blank"><?=$job->customer->name?></a></td>
            </tr>
            <tr>
                <th width="30%" scope="row">เบอร์โทร</th>
                <td><?=$job->customer->mobile?></td>
            </tr>
            <tr>
                <th width="30%" scope="row">Line</th>
                <td><?=$job->customer->line_id?></td>
            </tr>
            <tr>
                <th width="30%" scope="row">username</th>
                <td><?php 
                    if(!empty($job->customer_user->username)){
                        echo $job->customer_user->username ;
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th width="30%" scope="row">เกมส์</th>
                <td>
                    <?php 
                    if(!empty($job->customer_user->games)){
                        echo $job->customer_user->games->name ;
                    }
                    ?>
                </td>
            </tr>
            
        </tbody>
    </table>
</div>
