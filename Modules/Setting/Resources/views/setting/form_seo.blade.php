<div class="tab-pane fade" id="seo_tab" role="tabpanel">

    {{-- start tap --}}
    <div class="mb-3">
        <?= image_upload($id = '6', $name = 'seo_image', $label = __('websetting_admin.upload_image_header') . ' Favicon', $image = !empty($setting->seo_image) ? $setting->seo_image : '', $size_recommend = '32 x 32px', $accept = 'image') ?>
    </div>
    <div class="mb-3">
        <label class="form-label"
            title="{{ __('websetting_admin.meta_title') }}">{{ __('websetting_admin.meta_title') }}</label>
        <input type="text" class="form-control" id="meta_title" name="meta_title"
            placeholder="{{ __('websetting_admin.meta_title') }}"
            value="{{ !empty($setting->meta_title) ? $setting->meta_title : '' }}">
    </div>
    <div class="mb-3">
        <label class="form-label"
            title="{{ __('websetting_admin.meta_keywords') }}">{{ __('websetting_admin.meta_keywords') }}</label>
        <textarea class="form-control" id="meta_keywords" name="meta_keywords" rows="3"
            placeholder="{{ __('websetting_admin.meta_keywords') }}">{{ !empty($setting->meta_keywords) ? $setting->meta_keywords : '' }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label"
            title="{{ __('websetting_admin.meta_description') }}">{{ __('websetting_admin.meta_description') }}</label>
        <textarea class="form-control" id="meta_description" name="meta_description" rows="3"
            placeholder="{{ __('websetting_admin.meta_description') }}">{{ !empty($setting->meta_description) ? $setting->meta_description : '' }}</textarea>
    </div>
    {{-- end tap --}}
</div>
