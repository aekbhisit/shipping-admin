{{-- seo form th --}}
<div class="tab-pane fade show active" id="seo_tab_th" role="tabpanel">
    <input type="hidden" name="metadata[th][module]" value="<?=$metadata['th']['module']?>">
    <input type="hidden" name="metadata[th][method]" value="<?=$metadata['th']['method']?>">
    <input type="hidden" name="metadata[th][level]"  value="<?=$metadata['th']['level']?>">
    <div class="form-group">
        <label for="basic-url" class="form-label">{{ __('product::metadata.slug') }} (TH)</label>
        <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon3">{{ Request::root() }}/</span>
            <input type="text" class="form-control" name="metadata[th][slug]" id="metadata_th_slug" aria-describedby="basic-addon3" placeholder="{{ __('product::metadata.slug_placeholder') }}"
                value="{{ !empty($metadata['th']['slug']) ? $metadata['th']['slug'] : '' }}">
            <button class="btn btn-outline-secondary" type="button" id="button-addon2">Generate</button>
        </div>
    </div>

    <div class="form-group frm-name">
        <label class="form-label">{{ __('product::metadata.meta_title') }} (TH)</label>
        <input type="text" class="form-control" name="metadata[th][meta_title]" id="metadata_th_title" placeholder="{{ __('product::metadata.meta_title_placeholder') }} title"
            value="{{ !empty($metadata['th']['meta_title']) ? $metadata['th']['meta_title'] : '' }}">
    </div>
    <div class="form-group frm-name">
        <label class="form-label">{{ __('product::metadata.meta_keyword') }} (TH)</label>
        <input type="text" class="form-control" name="metadata[th][meta_keywords]" id="metadata_th_keywords"  placeholder="{{ __('product::metadata.meta_keyword_placeholder') }}"
            value="{{ !empty($metadata['th']['meta_keywords']) ? $metadata['th']['meta_keywords'] : '' }}">
    </div>
    <div class="form-group frm-name">
        <label class="form-label">{{ __('product::metadata.meta_description') }}
            (TH)</label>
        <input type="text" class="form-control" name="metadata[th][meta_description]" id="metadata_th_description" placeholder="{{ __('product::metadata.meta_description_placeholder') }}"
            value="{{ !empty($metadata['th']['meta_description']) ? $metadata['th']['meta_description'] : '' }}">
    </div>

</div>
{{-- seo form th --}}
