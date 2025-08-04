<div class="tab-pane fade show active" id="detail_tab" role="tabpanel">
    <table class="table table-striped-columns border">
        <tr>
            <td width="35%" class="text-capitalize m-0">SMS</td>
            <td width="65%">
                {{ !empty($data->sms_id) ? $data->sms_id : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize m-0">Temp</td>
            <td width="65%">
                {{ !empty($data->temp_id) ? $data->temp_id : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize m-0">Account</td>
            <td width="65%">
                {{ !empty($data->acc_id) ? $data->acc_id : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize m-0">bank balance</td>
            <td width="65%">
                {{ !empty($data->bank_balance) ? $data->bank_balance : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize m-0">hash text</td>
            <td width="65%">
                {{ !empty($data->hash_text) ? $data->hash_text : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize m-0">hash code</td>
            <td width="65%">
                {{ !empty($data->hash_code) ? $data->hash_code : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize m-0">app hash text</td>
            <td width="65%">
                {{ !empty($data->app_hash_text) ? $data->app_hash_text : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize m-0">app hash code</td>
            <td width="65%">
                {{ !empty($data->app_hash_code) ? $data->app_hash_code : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize m-0">app hash check</td>
            <td width="65%">
                {{ !empty($data->app_hash_check) ? $data->app_hash_check : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize m-0">member id</td>
            <td width="65%">
                {{ !empty($data->member_id) ? $data->member_id : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize m-0">audit note</td>
            <td width="65%">
                {{ !empty($data->sms_id) ? $data->sms_id : '-' }}
                {{ !empty($data->audit_note) ? $data->audit_note : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize m-0">tran id</td>
            <td width="65%">
                {{ !empty($data->tran_id) ? $data->tran_id : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize m-0">job id</td>
            <td width="65%">
                {{ !empty($data->job_id) ? $data->job_id : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize m-0">user id</td>
            <td width="65%">
                {{ !empty($data->user_id) ? $data->user_id : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize m-0">statement at</td>
            <td width="65%">
                {{ !empty($data->statement_at) ? date('Y-m-d H:i', strtotime($data->statement_at)) : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize m-0">statement by</td>
            <td width="65%">
                {{ !empty($data->statement_by) ? $data->statement_by : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize m-0">notes</td>
            <td width="65%">
                {{ !empty($data->notes) ? $data->notes : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize m-0">notes at</td>
            <td width="65%">
                {{ !empty($data->notes_at) ? date('Y-m-d H:i', strtotime($data->notes_at)) : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize m-0">notes by</td>
            <td width="65%">
                {{ !empty($data->notes_by) ? $data->notes_by : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize m-0">created at</td>
            <td width="65%">
                {{ !empty($data->created_at) ? date('Y-m-d H:i', strtotime($data->created_at)) : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize m-0">updated at</td>
            <td width="65%">
                {{ !empty($data->updated_at) ? date('Y-m-d H:i', strtotime($data->updated_at)) : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize m-0">source from</td>
            <td width="65%">
                {{ !empty($data->source_from) ? $data->source_from : '-' }}
            </td>
        </tr>
        <tr>
            <td width="35%" class="text-capitalize m-0">match bank</td>
            <td width="65%">
                @if (!empty($data->match_bank))

                    <div class="card">
                        @foreach ($data->match_bank as $match)
                            <div class="card-body">
                                username :
                                {{ !empty($match['username']) ? $match['username'] : '-' }}
                                <br>
                                name : {{ !empty($match['name']) ? $match['name'] : '-' }}<br>
                                mobile : {{ !empty($match['mobile']) ? $match['mobile'] : '-' }}
                            </div>
                        @endforeach
                    </div>
                @endif
            </td>
        </tr>
    </table>
</div>
