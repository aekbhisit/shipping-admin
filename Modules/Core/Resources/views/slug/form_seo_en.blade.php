{{-- Tap 1 --}}
<div class="tab_content">
    <div class="row">
        <div class="form-group col-md-3">
            <label class="form-label">level</label>
            <input type="text" class="form-control" name="metadata[en][level]" placeholder="level"
                value="{{ !empty($metadata['en']['level']) ? $metadata['en']['level'] : '' }}" <?=$mode=='edit'?'readonly':''?> >
        </div>
        <div class="form-group col-md-3">
            <label class="form-label">Module (EN)</label>
            <input type="text" class="form-control" name="metadata[en][module]" placeholder="Module"
                value="{{ !empty($metadata['en']['module']) ? $metadata['en']['module'] : '' }}" <?=$mode=='edit'?'readonly':''?>>
        </div>
        <div class="form-group col-md-3">
            <label class="form-label">Method (EN)</label>
            <input type="text" class="form-control" name="metadata[en][method]" placeholder="Method"
                value="{{ !empty($metadata['en']['method']) ? $metadata['en']['method'] : '' }}" <?=$mode=='edit'?'readonly':''?>>
        </div>
        <div class="form-group col-md-3">
            <label class="form-label">Data ID (EN)</label>
            <input type="text" class="form-control" name="metadata[en][data_id]" placeholder="Data ID"
                value="{{ !empty($metadata['en']['data_id']) ? $metadata['en']['data_id'] : '' }}" <?=$mode=='edit'?'readonly':''?>>
        </div>
    </div>
    <div class="form-group">
        <?= image_upload(
            $id = 2, 
            $name = 'meta_image_en', 
            $label = __('websetting_admin.upload_image_header'), 
            $image = !empty($metadata['en']['meta_image']) ? $metadata['en']['meta_image'] : '', 
            $size_recommend = '48 x 48px')
        ?>
    </div>
    <div class="form-group frm-name">
        <label class="form-label">Slug (EN)</label>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">{{ Request::root() }}/en/</span>
            </div>
            <input type="text" class="form-control" name="metadata[en][slug]" placeholder="Slug"
                value="{{ !empty($metadata['en']['slug']) ? $metadata['en']['slug'] : '' }}">
        </div>
    </div>

    <div class="form-group frm-name">
        <label class="form-label">Meta title (EN)</label>
        <input type="text" class="form-control" name="metadata[en][meta_title]" placeholder="Meta title"
            value="{{ !empty($metadata['en']['meta_title']) ? $metadata['en']['meta_title'] : '' }}">
    </div>
    <div class="form-group frm-name">
        <label class="form-label">Meta Keyword (EN)</label>
        <input type="text" class="form-control" name="metadata[en][meta_keywords]" placeholder="Meta Keyword"
            value="{{ !empty($metadata['en']['meta_keywords']) ? $metadata['en']['meta_keywords'] : '' }}">
    </div>
    <div class="form-group frm-name">
        <label class="form-label">Meta Description (EN)</label>
        <input type="text" class="form-control" name="metadata[en][meta_description]" placeholder="Meta Description"
            value="{{ !empty($metadata['en']['meta_description']) ? $metadata['en']['meta_description'] : '' }}">
    </div>

    <div class="form-group frm-name">
        <label class="form-label">Meta Auther (EN)</label>
        <input type="text" class="form-control" name="metadata[en][meta_auther]" placeholder="Meta Auther"
            value="{{ !empty($metadata['en']['meta_auther']) ? $metadata['en']['meta_auther'] : '' }}">
    </div>
    <div class="form-group frm-name">
        <label class="form-label">Meta Robots (EN)</label>
        <input type="text" class="form-control" name="metadata[en][meta_robots]" placeholder="Meta Robots"
            value="{{ !empty($metadata['en']['meta_robots']) ? $metadata['en']['meta_robots'] : '' }}">
    </div>
</div>
{{-- End Tap 1 --}}
