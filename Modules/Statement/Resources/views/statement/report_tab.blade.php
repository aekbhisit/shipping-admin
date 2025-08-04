<div class="tab-pane fade" id="report_tab" role="tabpanel">
    <table class="table table-striped-columns border">
        <tr>
            <td width="35%" class="text-capitalize">time</td>
            <td width="65%">
                {{ !empty($data->report_time) ? date('H:i', strtotime($data->report_time)) : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize">datetime</td>
            <td width="65%">
                {{ !empty($data->report_datetime) ? date('Y-m-d', strtotime($data->report_datetime)) : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize">channel</td>
            <td width="65%">
                {{ !empty($data->report_channel) ? $data->report_channel : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize">value</td>
            <td width="65%">
                {{ !empty($data->report_value) ? $data->report_value : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize">detail</td>
            <td width="65%">
                {{ !empty($data->report_detail) ? $data->report_detail : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize">app detail</td>
            <td width="65%">
                {{ !empty($data->app_report_detail) ? $data->app_report_detail : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize">acc</td>
            <td width="65%">
                {{ !empty($data->report_acc) ? $data->report_acc : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize">name</td>
            <td width="65%">
                {{ !empty($data->report_name) ? $data->report_name : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize">same acc</td>
            <td width="65%">
                {{ !empty($data->report_same_acc) ? $data->report_same_acc : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize">check</td>
            <td width="65%">
                {{ !empty($data->report_check) ? $data->report_check : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize">hash</td>
            <td width="65%">
                {{ !empty($data->report_hash) ? $data->report_hash : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize">row</td>
            <td width="65%">
                {{ !empty($data->report_row) ? $data->report_row : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize">status</td>
            <td width="65%">
                {{ !empty($data->report_status) ? $data->report_status : '-' }}
            </td>
        </tr>

        <tr>
            <td width="35%" class="text-capitalize">app at</td>
            <td width="65%">
                {{ !empty($data->app_report_at) ? date('Y-m-d H:i', strtotime($data->app_report_at)) : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize">note</td>
            <td width="65%">
                {{ !empty($data->report_note) ? $data->report_note : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize m-0">send</td>
            <td width="65%">
                {{ !empty($data->report_send) ? $data->report_send : '-' }}
            </td>
        </tr>
    </table>
</div>
