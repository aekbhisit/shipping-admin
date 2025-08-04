{{-- seo form en --}}
<div class="tab-pane fade show" id="seo_tab_en" role="tabpanel">
    <input type="hidden" name="metadata[en][module]" value="<?=$metadata['en']['module']?>">
    <input type="hidden" name="metadata[en][method]" value="<?=$metadata['en']['method']?>">
    <input type="hidden" name="metadata[en][level]"  value="<?=$metadata['en']['level']?>">
    <div class="form-group">
        <label for="basic-url" class="form-label">{{ __('product::metadata.slug') }} (EN)</label>
        <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon3">{{ Request::root() }}/en/</span>
            <input type="text" class="form-control" name="metadata[en][slug]" id="metadata_th_slug" aria-describedby="basic-addon3" placeholder="{{ __('product::metadata.slug_placeholder') }}"
                value="{{ !empty($metadata['en']['slug']) ? $metadata['en']['slug'] : '' }}">
            <button class="btn btn-outline-secondary" type="button" id="button-addon2">Generate</button>
        </div>
    </div>

    <div class="form-group frm-name">
        <label class="form-label">{{ __('product::metadata.meta_title') }} (EN)</label>
        <input type="text" class="form-control" name="metadata[en][meta_title]" id="metadata_en_title"  placeholder="Meta title"
            value="{{ !empty($metadata['en']['meta_title']) ? $metadata['en']['meta_title'] : '' }}">
    </div>
    <div class="form-group frm-name">
        <label class="form-label">{{ __('product::metadata.meta_keyword') }} (EN)</label>
        <input type="text" class="form-control" name="metadata[en][meta_keywords]" id="metadata_en_keywords" placeholder="Meta Keyword"
            value="{{ !empty($metadata['en']['meta_keywords']) ? $metadata['en']['meta_keywords'] : '' }}">
    </div>
    <div class="form-group frm-name">
        <label class="form-label">{{ __('product::metadata.meta_description') }}
            (EN)</label>
        <input type="text" class="form-control" name="metadata[en][meta_description]" id="metadata_en_description"  placeholder="Meta Description"
            value="{{ !empty($metadata['en']['meta_description']) ? $metadata['en']['meta_description'] : '' }}">
    </div>

</div>
{{-- .seo form en --}}
