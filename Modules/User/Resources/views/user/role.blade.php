{{-- @if ($enable_feature['role']) --}}
{{-- && mwz_roles('admin.user.user.role') && (!empty($user->id) && $user->id != 1) --}}
<label class="form-label">{{ __('user::lang.role') }}</label>
<ul id="show_roles" class="treeview">
    @foreach ($roles as $rm_k => $rm_p)
        <li><a href="#">@lang("user::lang.$rm_k.name")</a>
            <ul>
                @foreach ($rm_p as $rmm_k => $rmm_p)
                    <li>
                        <div class="d-flex">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input mt-2 {{ $rm_k . '_' . $rmm_k }}"
                                    name="role[{{ $rm_k }}][{{ $rmm_k }}][all]"
                                    id="{{ $rm_k . '_' . $rmm_k }}_all" value="all"
                                    onclick="setCheckAllPermission(this,'{{ $rm_k . '_' . $rmm_k }}');"
                                    {{ !empty($user->role[$rm_k][$rmm_k]) && in_array('all', $user->role[$rm_k][$rmm_k]) ? 'checked="checked"' : '' }}>
                                <label class="form-check-label" for="{{ $rm_k . '_' . $rmm_k }}_all">
                                    <strong>@lang("user::lang.$rm_k.$rmm_k")</strong>
                                </label>
                            </div>
                            <strong class="px-1">:</strong>
                            <div class="d-flex flex-wrap">
                                @foreach ($rmm_p as $rmmm_k => $rmmm_p)
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input mt-2 {{ $rm_k . '_' . $rmm_k }}"
                                            name="role[{{ $rm_k }}][{{ $rmm_k }}][{{ $rmmm_k }}]"
                                            id="{{ $rm_k . '_' . $rmm_k . '_' . $rmmm_k }}"
                                            value="{{ $rmmm_k }}"
                                            {{ !empty($user->role[$rm_k][$rmm_k]) && in_array($rmmm_k, $user->role[$rm_k][$rmm_k]) ? 'checked="checked"' : '' }}>
                                        <label class="form-check-label pe-1"
                                            for="{{ $rm_k . '_' . $rmm_k . '_' . $rmmm_k }}">
                                            @lang("user::lang.$rmmm_k")
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </li>
                @endforeach

            </ul>
        </li>
    @endforeach
</ul>
{{-- @else
    <input type="hidden" name="role[]" value="">
@endif --}}
