     <div class="row">
         <input type="hidden" name="data_id" value="{{ !empty($data_id) ? $data_id : '' }}">
         <div class="mb-3 col-md-3">
             <label class="form-label">level</label>
             <input type="text" class="form-control" name="metadata[level]" placeholder="Meta title"
                 value="{{ !empty($metadata['level']) ? $metadata['level'] : '' }}" readonly>
         </div>
         <div class="mb-3 col-md-3">
             <label class="form-label">Module (TH)</label>
             <input type="text" class="form-control" name="metadata[module]" placeholder="Module"
                 value="{{ !empty($metadata['module']) ? $metadata['module'] : '' }}" readonly>
         </div>
         <div class="mb-3 col-md-3">
             <label class="form-label">Method (TH)</label>
             <input type="text" class="form-control" name="metadata[method]" placeholder="Method"
                 value="{{ !empty($metadata['method']) ? $metadata['method'] : '' }}" readonly>
         </div>
         <div class="mb-3 col-md-3">
             <label class="form-label">Data ID (TH)</label>
             <input type="text" class="form-control" name="metadata[data_id]" placeholder="Data ID"
                 value="{{ !empty($metadata['data_id']) ? $metadata['data_id'] : '' }}" readonly>
         </div>
     </div>
     <div class="mb-3">
         <?= image_upload($id = 1, $name = 'meta_image', $label = __('admin.upload_image') . ' Favicon', $image = !empty($metadata['meta_image']) ? $metadata['meta_image'] : '', $size_recommend = '48 x 48px') ?>
     </div>
     <div class="mb-3 frm-name">
         {{-- <label class="form-label">Slug (TH)</label>
         <div class="input-group">
             <div class="input-group-prepend">
                 <span class="input-group-text">{{ Request::root() }}/</span>
             </div>
            </div> --}}
         <label for="slug" class="form-label">Slug (TH)</label>
         <div class="input-group mb-3">
             <span class="input-group-text" id="slug">{{ Request::root() }}</span>
             <input type="text" class="form-control" id="" name="metadata[slug]" placeholder="Slug"
                 value="{{ !empty($metadata['slug']) ? $metadata['slug'] : '' }}">
         </div>
     </div>

     <div class="mb-3 frm-name">
         <label class="form-label">Meta title (TH)</label>
         <input type="text" class="form-control" name="metadata[meta_title]" placeholder="Meta title"
             value="{{ !empty($metadata['meta_title']) ? $metadata['meta_title'] : '' }}">
     </div>
     <div class="mb-3 frm-name">
         <label class="form-label">Meta Keyword (TH)</label>
         <input type="text" class="form-control" name="metadata[meta_keywords]" placeholder="Meta Keyword"
             value="{{ !empty($metadata['meta_keywords']) ? $metadata['meta_keywords'] : '' }}">
     </div>
     <div class="mb-3 frm-name">
         <label class="form-label">Meta Description (TH)</label>
         <input type="text" class="form-control" name="metadata[meta_description]" placeholder="Meta Description"
             value="{{ !empty($metadata['meta_description']) ? $metadata['meta_description'] : '' }}">
     </div>
     <div class="mb-3 frm-name">
         <label class="form-label">Meta Auther (TH)</label>
         <input type="text" class="form-control" name="metadata[meta_auther]" placeholder="Meta Auther"
             value="{{ !empty($metadata['meta_auther']) ? $metadata['meta_auther'] : '' }}">
     </div>
     <div class="mb-3 frm-name">
         <label class="form-label">Meta Robots (TH)</label>
         <input type="text" class="form-control" name="metadata[meta_robots]" placeholder="Meta Robots"
             value="{{ !empty($metadata['meta_robots']) ? $metadata['meta_robots'] : '' }}">
     </div>
