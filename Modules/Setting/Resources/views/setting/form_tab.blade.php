<div class="tab-pane fade show active" id="setting_tab" role="presentation">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" data-bs-toggle="tab" href="#header" role="tab" aria-selected="true">
                <div class="tab-title">โลโก้(Header)</div>
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" data-bs-toggle="tab" href="#footer" role="tab" aria-selected="false" tabindex="-1">
                <div class="tab-title">โลโก้(Footer)</div>
            </a>
        </li>
    </ul>
    <div class="tab-content border border-top-0 p-3">
        <div class="tab-pane fade active show" id="header" role="tabpanel">
            <div class="alert alert-outline-secondary shadow-sm alert-dismissible fade show py-2">
                <div class="d-flex align-items-center">
                    <div class="font-20 text-secondary"><i class="bx bx-bell"></i>
                    </div>
                    <div class="ms-3">
                        คุณกำลังแก้ไข โลโก้(Header)
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <?= image_upload($id = '1', $name = 'logo_header', $label = __('websetting_admin.upload_image_header'), $image = !empty($setting->logo_header) ? $setting->logo_header : '', $size_recommend = ' 360 x 80px', $accept = 'image') ?>
            </div>
        </div>
        <div class="tab-pane fade" id="footer" role="tabpanel">
            <div class="alert alert-outline-secondary shadow-sm alert-dismissible fade show py-2">
                <div class="d-flex align-items-center">
                    <div class="font-20 text-secondary"><i class="bx bx-bell"></i>
                    </div>
                    <div class="ms-3">
                        คุณกำลังแก้ไข โลโก้(Footer)
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <?= image_upload($id = '2', $name = 'logo_footer', $label = __('websetting_admin.upload_image_header'), $image = !empty($setting->logo_footer) ? $setting->logo_footer : '', $size_recommend = ' 360 x 80px', $accept = 'image') ?>
            </div>
            <div class="mb-3">
                <label class="form-label" title="Copyright">{{ __('websetting_admin.copyright') }}</label>
                <input type="text" class="form-control" name="link_login" placeholder="Copyright"
                    value="{{ !empty($setting->link_login) ? $setting->link_login : '' }}">
            </div>
        </div>
    </div>
</div>
