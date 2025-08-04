{{-- Tap 1 --}}
<div class="tab_content active">
    <div class="row">
        <div class="form-group col-md-3">
            <label class="form-label">level</label>
            <input type="text" class="form-control" name="metadata[th][level]" placeholder="level"
                value="{{ !empty($metadata['th']['level']) ? $metadata['th']['level'] : '' }}" <?=$mode=='edit'?'readonly':''?>>
        </div>
        <div class="form-group col-md-3">
            <label class="form-label">Module (TH)</label>
            <input type="text" class="form-control" name="metadata[th][module]" placeholder="Module"
                value="{{ !empty($metadata['th']['module']) ? $metadata['th']['module'] : '' }}" <?=$mode=='edit'?'readonly':''?>>
        </div>
        <div class="form-group col-md-3">
            <label class="form-label">Method (TH)</label>
            <input type="text" class="form-control" name="metadata[th][method]" placeholder="Method"
                value="{{ !empty($metadata['th']['method']) ? $metadata['th']['method'] : '' }}" <?=$mode=='edit'?'readonly':''?>>
        </div>
        <div class="form-group col-md-3">
            <label class="form-label">Data ID (TH)</label>
            <input type="text" class="form-control" name="metadata[th][data_id]" placeholder="Data ID"
                value="{{ !empty($metadata['th']['data_id']) ? $metadata['th']['data_id'] : '' }}" <?=$mode=='edit'?'readonly':''?>>
        </div>
    </div>
    <div class="form-group">
        <?= image_upload($id = 1, 
        $name = 'meta_image_th', 
        $label = __('websetting_admin.upload_image_header'), 
        $image = !empty($metadata['th']['meta_image']) ? $metadata['th']['meta_image'] : '', 
        $size_recommend = '48 x 48px') ?>
    </div>
    <div class="form-group frm-name">
        <label class="form-label">Slug (TH)</label>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">{{ Request::root() }}/</span>
            </div>
            <input type="text" class="form-control" name="metadata[th][slug]" placeholder="Slug"
                value="{{ !empty($metadata['th']['slug']) ? $metadata['th']['slug'] : '' }}">
        </div>
    </div>

    <div class="form-group frm-name">
        <label class="form-label">Meta title (TH)</label>
        <input type="text" class="form-control" name="metadata[th][meta_title]" placeholder="Meta title"
            value="{{ !empty($metadata['th']['meta_title']) ? $metadata['th']['meta_title'] : '' }}">
    </div>
    <div class="form-group frm-name">
        <label class="form-label">Meta Keyword (TH)</label>
        <input type="text" class="form-control" name="metadata[th][meta_keywords]" placeholder="Meta Keyword"
            value="{{ !empty($metadata['th']['meta_keywords']) ? $metadata['th']['meta_keywords'] : '' }}">
    </div>
    <div class="form-group frm-name">
        <label class="form-label">Meta Description (TH)</label>
        <input type="text" class="form-control" name="metadata[th][meta_description]" placeholder="Meta Description"
            value="{{ !empty($metadata['th']['meta_description']) ? $metadata['th']['meta_description'] : '' }}">
    </div>
    <div class="form-group frm-name">
        <label class="form-label">Meta Auther (TH)</label>
        <input type="text" class="form-control" name="metadata[th][meta_auther]" placeholder="Meta Auther"
            value="{{ !empty($metadata['th']['meta_auther']) ? $metadata['th']['meta_auther'] : '' }}">
    </div>
    <div class="form-group frm-name">
        <label class="form-label">Meta Robots (TH)</label>
        <input type="text" class="form-control" name="metadata[th][meta_robots]" placeholder="Meta Robots"
            value="{{ !empty($metadata['th']['meta_robots']) ? $metadata['th']['meta_robots'] : '' }}">
    </div>

</div>
{{-- End Tap 1 --}}
