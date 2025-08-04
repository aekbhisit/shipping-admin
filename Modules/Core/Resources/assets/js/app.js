// init script
MULTI_UPLOAD = {} ;

$(document).ready(function() {
  // init multi file upload
  init_maps_picker();
  int_datetimepicker();
  int_datepicker();

  init_texteditor();

  select2();
  select2AjaxWithImage();
  select2AjaxWithImageMultiple();
  select2AjaxTags();

  init_upload();
  init_multi_upload();
  init_filebrowse();

  set_content_tab_init();
  set_nav_tab_init();

});



global_loading = function(show = 1) {
  if (show) {
    $("#global-loader").fadeIn(100);
  } else {
    $("#global-loader").fadeOut(100);
  }
};

// notification
noti = function (type, msg) {
  console.log('core_noti' + type + ' ' + msg)
  switch (type) {
    case 'success':
      return Lobibox.notify('success', {
        size: 'mini',
        icon: 'bx bx-check-circle',
        delayIndicator: false,
        continueDelayOnInactiveTab: false,
        position: 'top right',
        msg: msg,
      })
      break
    case 'error':
      return Lobibox.notify('error', {
        size: 'mini',
        delayIndicator: false,
        icon: 'bx bx-x-circle',
        continueDelayOnInactiveTab: false,
        position: 'top right',
        msg: msg,
      })
      break
    case 'warning':
      return Lobibox.notify('warning', {
        size: 'mini',
        delayIndicator: false,
        icon: 'bx bx-error',
        continueDelayOnInactiveTab: false,
        position: 'top right',
        msg: msg,
      })
      break
    case 'info':
      return Lobibox.notify('info', {
        size: 'mini',
        icon: 'bx bx-info-circle',
        delayIndicator: false,
        continueDelayOnInactiveTab: false,
        position: 'top right',
        msg: msg,
      })
      break
    default:
      return Lobibox.notify('default', {
        size: 'mini',
        delayIndicator: false,
        continueDelayOnInactiveTab: false,
        position: 'top right',
        msg: msg,
      })
      break
  }
}

JsReload = function (time) {
  setInterval(function () {
    window.location.reload()
  }, time)
}

JsRedirect = function (url, time) {
  setInterval(function () {
    window.location.replace(url)
  }, time)
}

// text editor
// (function($) {
  // init texteditor
function init_texteditor(){
  if ($(".texteditor").length > 0) {
    tinymce.init({
      selector: "textarea.texteditor",
      skin: "lightgray",
      themes: "modern",
      inline: false,
      height: 250,
      mode: "textareas",
      relative_urls: true,
      extended_valid_elements:
        "i[class|style|title],iframe[src|frameborder|style|scrolling|class|width|height|name|align]",
      fontsize_formats:
        "8pt 10pt 11pt 12pt 13pt 14pt 15pt 16pt 17pt 18pt 19pt 20pt 21pt 22pt 23pt 24pt 25pt 26pt 28pt 30pt 32pt 34pt 36pt",
      file_browser_callback: elFinderBrowser,
      plugins: [
        "advlist autoresize autolink lists link image charmap print preview anchor emoticons",
        "searchreplace visualblocks code fullscreen",
        "insertdatetime media table contextmenu paste textcolor hr template",
      ],
      autoresize_overflow_padding: 5,
      autoresize_min_height: 250,
      autoresize_max_height: 500,
      extended_valid_elements:
        "i[class|style|title],span[class|style|title],a[accesskey|charset|class|contenteditable|contextmenu|coords|dir|download|draggable|dropzone|hidden|href|hreflang|id|lang|media|name|rel|rev|shape|spellcheck|style|tabindex|target|title|translate|type|onclick|onfocus|onblur],marquee",
      toolbar1: " insertfile bootstrap ",
      toolbar2:
        " undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media ",
      toolbar3:
        " forecolor backcolor | formatselect styleselect fontsizeselect | emoticons fontawesome | template",
      content_style:
        ".container .row .col-xs-12{position:relative}.container .row .col-xs-12::before{content:'';display:block;position:absolute;height:100%;width:calc(100% - 30px);border:1px dotted gray}.container .row .col-xs-12 img{max-width:100%;height:auto}",
      templates: [
        {
          title: "1 columns",
          description: "Insert on the page, 1 columns with the same width",
          content:
            '<div class="container"><div class="row"><div class="col-xs-12 col-sm-12 col-md-12"><p>Column 1</p></div></div></div><p></p>',
        },
        {
          title: "2 columns",
          description: "Insert on the page, 2 columns with the same width",
          content:
            '<div class="container"><div class="row"><div class="col-xs-12 col-sm-12 col-md-6"><p>Column 1</p></div><div class="col-xs-12 col-sm-12 col-md-6"><p>Column 2</p></div></div></div><p></p>',
        },
        {
          title: "3 columns",
          description: "Insert on the page, 3 columns with the same width",
          content:
            '<div class="container"><div class="row"><div class="col-xs-12 col-sm-12 col-md-4">Column 1</div><div class="col-xs-12 col-sm-12 col-md-4">Column 2</div><div class="col-xs-12 col-sm-12 col-md-4"><p>Column 3</p></div></div></div><p></p>',
        },
        {
          title: "4 columns",
          description: "Insert on the page, 4 columns with the same width",
          content:
            '<div class="container"><div class="row"><div class="col-xs-12 col-sm-12 col-md-3">Column 1</div><div class="col-xs-12 col-sm-12 col-md-3">Column 2</div><div class="col-xs-12 col-sm-12 col-md-3"><p>Column 3</p></div><div class="col-xs-12 col-sm-12 col-md-3"><p>Column 4</p></div></div></div><p></p>',
        },
        {
          title: "6 columns",
          description: "Insert on the page, 6 columns with the same width",
          content:
            '<div class="container"><div class="row"><div class="col-xs-12 col-sm-12 col-md-2">Column 1</div><div class="col-xs-12 col-sm-12 col-md-2">Column 2</div><div class="col-xs-12 col-sm-12 col-md-2"><p>Column 3</p></div><div class="col-xs-12 col-sm-12 col-md-2"><p>Column 4</p></div><div class="col-xs-12 col-sm-12 col-md-2">Column 5</div><div class="col-xs-12 col-sm-12 col-md-2"><p>Column 6</p></div></div></div><p></p>',
        },
      ],
    });
  }
}

// })(jQuery);


function elFinderBrowser(field_name, url, type, win) {
  tinymce.activeEditor.windowManager.open(
    {
      file: "/admin/elfinder/tinymce4", // use an absolute path!
      title: "File Management",
      width: 900,
      height: 450,
      resizable: "yes",
    },
    {
      setUrl: function (url) {
        win.document.getElementById(field_name).value = url;
        console.log(url);
      },
    }
  );
  return false;
}

// form validation
frm_validate = function (selector, rules, messages) {
  var valid_result = $(selector).validate({
    ignore: "",
    rules: rules,
    messages: messages,
    errorPlacement: function (error, element) {
      error.addClass("invalid-feedback");
      if (element.prop("type") === "checkbox") {
        error.insertAfter(element.next("label"));
      } else {
        error.insertAfter(element);
      }
    },
    invalidHandler: function (form, validator) {
      var errors = validator.numberOfInvalids();
      if (errors) {
        var first_invalid = validator.errorList[0].element;
        var tab_pane = $(first_invalid).closest("div.tab-pane");
        console.log(tab_pane);
        if (typeof tab_pane != 'undefined') {
          var tab_index = $(tab_pane).index();
          console.log('tab_index' + tab_index);
          var tab_nav = $(tab_pane).closest("div.tab-content").prev("ul.nav-tabs") ;
          localStorage.setItem($(tab_nav).attr("id") + "_activeTab",tab_index);
          $(tab_nav).find('li').eq(tab_index).find('a').tab('show');
        }      
        
        var tab_main_pane = $(tab_nav).closest("div.tab-pane");
        if (typeof tab_main_pane != 'undefined' ){
          var tab_main_index = $(tab_main_pane).index();
          console.log('tab_main_index' + tab_main_index);
          var tab_main_nav = $(tab_main_pane).closest("div.tab-content").prev("ul.nav-tabs") ;
          localStorage.setItem($(tab_main_nav).attr("id") + "_activeTab", tab_main_index);
          $(tab_main_nav).find("li").eq(tab_main_index).find('a').tab('show');
        }
      }
    },
    errorElement: "div",
    errorClass: "invalid-feedback",
    highlight: function (element, errorClass, validClass) {
      $(element)
        .addClass("is-invalid")
        .removeClass("is-valid");
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element)
        .addClass("is-valid")
        .removeClass("is-invalid");
    },
  });
};

// map
init_maps_picker = function() {
  $(".maps_picker").each(function() {
    var ele = $(this);
    console.log($(ele).attr("class"));
    $(this).PlacePicker({
      btnClass: "btn btn-xs btn-default",
      key: "AIzaSyASUQHNsNID4l1HkBuawQGqaJ9rtUU-HqI",
      center: { lat: 13.758036276095279, lng: 100.56483464997967 },
      success: function(data, address) {
        // console.log($(this).html());
        console.log(data.currentLocation);
        console.log(data.formatted_address);
        $(ele).val(data.formatted_address);
      },
    });
  });
};

select2 = function (){
  if ($(".select2").length > 0) {
    $(".select2").select2({
      theme: "bootstrap-5",
      minimumResultsForSearch: Infinity,
    });
  }

  if($(".select2-show-search").length > 0) {
    console.log('.select2-show-search');
    $(".select2-show-search").select2({
      theme: "bootstrap-5",
      minimumResultsForSearch: "",
    });
  }
  
}

// select2
select2AjaxWithImage = function( selecter = '.select2-ajax-with-image',reset=false,){
  console.log('select2AgaxwithImage');
  $(document).find(selecter).each(function(index){
      let _token = $('meta[name="csrf-token"]').attr("content");
      var ele = this ;
      if(reset){
        if ($(this).hasClass("select2-hidden-accessible")) {
          $(this).select2('destroy');
        }
      }

      if (!$(this).hasClass("select2-hidden-accessible")) {
          var ajax_url =  $(this).data('ajax-url') ;
          var lang_placeholder =  $(this).data('lang-placeholder') ;
          var lang_searching =  $(this).data('lang-searching') ;
          var parent_val = "";
          var parent_id =  $(this).data('parent-id') ;
          if(parent_id!=""){
            parent_val = $('#'+parent_id).val() ;
          }

          var $select_ele = $(this).select2({
              theme: "bootstrap-5",
              placeholder: lang_placeholder,
              allowClear: true,
              templateResult: formatSelect2Result,
              templateSelection: formatSelect2Selected,
              minimumInputLength: 0,
              language: {
                  searching: function() {
                      return lang_searching ;
                  }
              },
              ajax: {
                  url: ajax_url,
                  type: "POST",
                  data: function (params) {
                      var query = {
                          search: params.term,
                          parent: parent_val,
                          _token: _token
                      }
                      return query;
                  }
              }
          });

          var selected_option_id = $(this).data('selected-id') ; 
          var selected_option_text = $(this).data('selected-text') ; 
          var selected_option_img = $(this).data('selected-image') ; 
          if(selected_option_id!=""){
              var data = {
                  id: selected_option_id,
                  text: selected_option_text,
                  image: selected_option_img
              };
              var newOption = new Option(data.text, data.id, true, true);
              $(newOption).attr('data-image',data.image) ;
              $($select_ele).append(newOption).trigger('change');
          }
      }
     
  });  
}

select2AjaxWithImageMultiple = function( selecter = '.select2-ajax-with-image-multiple',reset=false,){
  console.log('select2AjaxWithImageMultiple');
  $(document).find(selecter).each(function(index){
      let _token = $('meta[name="csrf-token"]').attr("content");
      var ele = this ;
      if(reset){
        if ($(this).hasClass("select2-hidden-accessible")) {
          $(this).select2('destroy');
        }
      }

      if (!$(this).hasClass("select2-hidden-accessible")) {
          var ajax_url =  $(this).data('ajax-url') ;
          var lang_placeholder =  $(this).data('lang-placeholder') ;
          var lang_searching =  $(this).data('lang-searching') ;
          var parent_val = "";
          var parent_id =  $(this).data('parent-id') ;
          if(parent_id!=""){
            parent_val = $('#'+parent_id).val() ;
          }

          var $select_ele = $(this).select2({
              theme: "bootstrap-5",
              placeholder: lang_placeholder,
              allowClear: true,
              templateResult: formatSelect2Result,
              templateSelection: formatSelect2Selected,
              minimumInputLength: 0,
              multiple:true,
              language: {
                  searching: function() {
                      return lang_searching ;
                  }
              },
              ajax: {
                  url: ajax_url,
                  type: "POST",
                  data: function (params) {
                      var query = {
                          search: params.term,
                          parent: parent_val,
                          _token: _token
                      }
                      return query;
                  }
              }
          });

          var selected_option_id = $(this).data('selected-id') ; 
          var selected_option_text = $(this).data('selected-text') ; 
          var selected_option_img = $(this).data('selected-image') ; 

          if(selected_option_id!=""){
              var arr_option_id = selected_option_id.split(",");
              var arr_option_text = selected_option_text.split(",");
              var arr_option_img = selected_option_img.split(",");

              $.each(array,function(i){
                  var data = {
                    id: arr_option_id[i],
                    text: arr_option_text[i],
                    image: arr_option_img[i]
                };
                var newOption = new Option(data.text, data.id, true, true);
                $(newOption).attr('data-image',data.image) ;
                $($select_ele).append(newOption).trigger('change');
              });

             
          }
      }
     
  });  
}

select2AjaxTags = function( selecter = '.select2-ajax-tags',reset=false,){
  console.log('select2AjaxTags');
  $(document).find(selecter).each(function(index){
      let _token = $('meta[name="csrf-token"]').attr("content");
      var ele = this ;
      if(reset){
        if ($(this).hasClass("select2-hidden-accessible")) {
          $(this).select2('destroy');
        }
      }

      if (!$(this).hasClass("select2-hidden-accessible")) {
          var ajax_url =  $(this).data('ajax-url') ;
          var lang_placeholder =  $(this).data('lang-placeholder') ;
          var lang_searching =  $(this).data('lang-searching') ;
          var parent_val = "";
          var parent_id =  $(this).data('parent-id') ;
          if(parent_id!=""){
            parent_val = $('#'+parent_id).val() ;
          }

          var $select_ele = $(this).select2({
              theme: "bootstrap-5",
              placeholder: lang_placeholder,
              allowClear: true,
              // templateResult: formatSelect2TagResult,
              // templateSelection: formatSelect2TagSelected,
              minimumInputLength: 0,
              multiple:true,
              tags:true,
              tokenSeparators: [','],
              language: {
                  searching: function() {
                      return lang_searching ;
                  }
              },
              ajax: {
                  url: ajax_url,
                  type: "POST",
                  data: function (params) {
                      var query = {
                          search: params.term,
                          parent: parent_val,
                          _token: _token
                      }
                      return query;
                  }
              },
              createTag: function (params) {
                console.log('createTag');
                console.log(params);
                var term = $.trim(params.term);

                if (term === '') {
                  return null;
                }

                return {
                  id: term,
                  text: term,
                  newTag: true // add additional parameters
                }
              },
              insertTag: function (data, tag) {
                console.log('insertTag');
                console.log(data);
                console.log(tag);

                // Insert the tag at the end of the results
                data.push(tag);

              }
          });

          var selected_option_id = $(this).data('selected-id') ; 
          var selected_option_text = $(this).data('selected-text') ; 

          if(selected_option_id!=""){
              var arr_option_id =  selected_option_id.split(",") ;
              $.each(arr_option_id,function(i,tag){
                  var data = {
                    id: tag, 
                    text: tag ,
                };
                var newOption = new Option(data.text, data.id, true, true);
                $($select_ele).append(newOption).trigger('change');
              });   
          }
      }
     
  });  
}

function formatSelect2Result(opt,container) {
  var image_url = $(opt.element).attr('data-image');
  if (typeof opt.image== 'undefined' || opt.image=="") {
    var $opt = $(
        '<span>' + opt.text + '</span>'
        );
  }else{
    var $opt = $(
        '<span><img src="' + opt.image + '" class="img-flag" width="30px"/> ' + opt.text + '</span>'
        );
  }

  return $opt;
};

function formatSelect2Selected(opt,container) {

  var image_url = $(opt.element).attr('data-image');
  if (typeof opt.image != 'undefined' && opt.image!='') {
      var $opt = $(
          '<span><img src="' + opt.image + '" class="img-flag otp-img" width="30px"/> ' + opt.text + '</span>'
          );
      return $opt;
  }else if(typeof image_url != 'undefined' && image_url!='') {
      var $opt = $(
      '<span><img src="' + image_url + '" class="img-flag img-url" width="30px"/> ' + opt.text + '</span>'
      );
      return $opt;   
  }else{
      var $opt = $(
        '<span>' + opt.text + '</span>'
      );
      return $opt;  
  } 
};

function formatSelect2TagResult(opt,container) {
  opt.newTag= true 
  var image_url = $(opt.element).attr('data-image');
  if (typeof opt.image== 'undefined' || opt.image=="") {
    var $opt = $(
        '<span>' + opt.text + '</span>'
        );
  }else{
    var $opt = $(
        '<span><img src="' + opt.image + '" class="img-flag" width="30px"/> ' + opt.text + '</span>'
        );
  }

  return $opt;
};

function formatSelect2TagSelected(opt,container) {
  opt.newTag= true
  var image_url = $(opt.element).attr('data-image');
  if (typeof opt.image != 'undefined') {
      var $opt = $(
          '<span><img src="' + opt.image + '" class="img-flag" width="30px"/> ' + opt.text + '</span>'
          );
      return $opt;
  }else if(typeof image_url != 'undefined') {
      var $opt = $(
      '<span><img src="' + image_url + '" class="img-flag" width="30px"/> ' + opt.text + '</span>'
      );
      return $opt;   
  }else{
      var $opt = $(
        '<span>' + opt.text + '</span>'
      );
      return $opt;  
  } 
};


int_datetimepicker = function() {
  if ($(".datetimepicker").length > 0) {
    $(".datetimepicker").datetimepicker({ keepOpen: true});
  }
};

int_datepicker = function () {
  if ($(".datepicker").length > 0) {
    $(".datepicker").datetimepicker({ keepOpen: true, format: 'YYYY-MM-DD' });
  }
};

function initFileUpload() {
  if ($("input.fileUpload").length > 0) {
    $(document)
      .find("input.fileUpload")
      .each(function() {
        if ($(this).attr("data-active") != "true") {
          var str_to_boolean = new Array();
          str_to_boolean["false"] = false;
          str_to_boolean["true"] = true;
          var m_f_s = $(this).attr("data-maxfilesize");
          var a_f_u = $(this).attr("data-allowfile");
          var storage = $(this).attr("data-storage");
          var input = $(this);
          var input_id = $(this).attr("id");
          var input_name = $(this).attr("name");
          var data_multiple = str_to_boolean[$(this).attr("data-multiple")];
          var data_autostart = str_to_boolean[$(this).attr("data-autostart")];
          var data_target = $(this).attr("data-target");
          var data_domainid = $(this).attr("data-dmid");
          var data_browse = $(this).attr("data-browse");
          var data_array_name = $(this).attr("data-array");
          var images_key = "_images";
          var delete_key = "_delete";
          var name_prefix = "";
          var is_array = false;
          if (str_to_boolean[data_array_name] == true) {
            images_key = "[images]";
            //delete_key = "[delete]";
            name_prefix = "array_";
            is_array = true;
          }
          var uploaded_queue = 0;
          var file_uploaded = [];
          var container =
            '<div id="' +
            input_id +
            '_container" class="fileUploadContainer"><input name="' +
            input_id +
            delete_key +
            '" id="' +
            input_id +
            delete_key +
            '" type="hidden" value="" /><button type="button" class="btn btn-info" id="' +
            input_id +
            '_btn_upload">' +
            data_browse +
            '</button></div><div id="' +
            input_id +
            '_filelist"></div>';
          $(this).after(container);
          var uploader = new plupload.Uploader({
            runtimes: "html5,silverlight,html4",
            browse_button: input_id + "_btn_upload", // you can pass in id...
            multi_selection: data_multiple,
            unique_names: true,
            container: document.getElementById("container"), // ... or DOM Element itself
            url: "/master/multifiles/upload",
            headers: {
              "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            flash_swf_url: "/plupload/js/Moxie.swf",
            silverlight_xap_url: "/plupload/js/Moxie.xap",
            filters: {
              max_file_size: m_f_s,
              mime_types: [{ title: "Image files", extensions: a_f_u }],
            },
            init: {
              PostInit: function() {
                if (input.val() != "") {
                  var img_arr = input.val().split(",");
                  plupload.each(img_arr, function(path) {
                    file = path.replace(storage + "/", "");
                    var file_dot = file.split(".");
                    var chk_dot = file_dot.length - 1;
                    var filename = file_dot[0];
                    file_dot = file_dot[1];

                    var src = "";
                    var url = "{{ route('admin.master.multifiles.src') }}";
                    var _token = $('meta[name="csrf-token"]').attr("content");
                    $.ajax({
                      type: "POST",
                      url: url,
                      data: { name: path, _token: _token },
                      dataType: "json",
                      success: function(data) {
                        document.getElementById(
                          input_id + "_filelist"
                        ).innerHTML +=
                          '<div id="' +
                          filename +
                          '" data-dot="' +
                          file_dot +
                          '" class="showUploadImageList" data-system="false"><img src="' +
                          data.data.src +
                          '" class="showUploadImg" id="img_' +
                          filename +
                          '" /><b class="showPercentage"></b><button class="btn btn-xs btn-danger btn-notification delete-image" id="btn_delete_' +
                          filename +
                          '" data-layout="top" data-type="confirm"  data-modal="true" type="button" onclick="setDeleteUploadFile(\'' +
                          filename +
                          "','" +
                          file_dot +
                          "','" +
                          storage +
                          "','" +
                          input_name +
                          "',this," +
                          is_array +
                          ');"><i class="icon-trash"></i></button><div style="clear:both;"></div><i class="filename"><div class="filename_block">' +
                          (filename + "." + file_dot) +
                          "</div></i></div>";
                      },
                    });
                  });
                }
                if (data_autostart == "false") {
                  document.getElementById(
                    input_id + "_btn_upload"
                  ).onclick = function() {
                    uploader.start();
                    return false;
                  };
                }
              },
              FilesAdded: function(up, files) {
                if (data_multiple == false) {
                  $(input).val("");
                  $("#" + input_name + "_filelist")
                    .find("div.showUploadImageList")
                    .each(function(index, element) {
                      var file_id = $(this).attr("id");
                      var file_dot = $(this).attr("data-dot");

                      setDeleteUploadFile(
                        file_id,
                        file_dot,
                        storage,
                        input_name,
                        $(this),
                        is_array
                      );
                    });
                }
                plupload.each(files, function(file) {
                  var file_dot = file.name.split(".");
                  var chk_dot = file_dot.length - 1;
                  file_dot = file_dot[chk_dot];
                  document.getElementById(input_id + "_filelist").innerHTML +=
                    '<div id="' +
                    file.id +
                    '" data-dot="' +
                    file_dot +
                    '" class="showUploadImageList" data-system="false"><img src="/assets/images/admin/ajax-loader.gif" class="showUploadImg" id="img_' +
                    file.id +
                    '" /><b class="showPercentage"></b><button class="btn btn-xs btn-danger btn-notification delete-image" id="btn_delete_' +
                    file.id +
                    '" data-layout="top" data-type="confirm"  data-modal="true" type="button" onclick="setDeleteUploadFile(\'' +
                    file.id +
                    "','" +
                    file_dot +
                    "','" +
                    storage +
                    "','" +
                    input_name +
                    "',this," +
                    is_array +
                    ');"><i class="icon-trash"></i></button><div style="clear:both;"></div><i class="filename"><div class="filename_block">' +
                    file.name +
                    " (" +
                    plupload.formatSize(file.size) +
                    ")</div></i></div>";
                });
                if (data_autostart == "true" || data_autostart === true) {
                  uploader.start();
                }
              },
              UploadProgress: function(up, file) {
                document
                  .getElementById(file.id)
                  .getElementsByTagName("b")[0].innerHTML =
                  "<span>" + file.percent + "%</span>";
              },
              UploadComplete: function(up, file) {
                $("#" + input_id + "_filelist").sortable({
                  placeholder: "sort-image-placeholder",
                  delay: 200,
                });
                // auto sort file when uplaod complete
                var sort_list = [];
                $("#" + input_id + "_filelist")
                  .find("div.showUploadImageList")
                  .each(function(index, element) {
                    sort_list.push(
                      storage +
                        "/" +
                        $(this).attr("id") +
                        "." +
                        $(this).attr("data-dot")
                    );
                  });
                $(input).val(sort_list.join(","));
                // sort file by drag&drop
                $("#" + input_id + "_filelist").bind("sortstop", function(
                  event,
                  ui
                ) {
                  var sort_list = [];
                  $(this)
                    .find("div.showUploadImageList")
                    .each(function(index, element) {
                      sort_list.push(
                        storage +
                          "/" +
                          $(this).attr("id") +
                          "." +
                          $(this).attr("data-dot")
                      );
                    });
                  $(input).val(sort_list.join(","));
                });
              },
              FileUploaded: function(up, file, info) {
                var file_dot = file.name.split(".");
                var chk_dot = file_dot.length - 1;
                var file_name = file.id + file_dot;
                var obj = JSON.parse(info.response);
                // var src = storagePath + file_name;
                var src = "";
                var url = "{{ route('admin.master.multifiles.src') }}";
                var _token = $('meta[name="csrf-token"]').attr("content");
                $.ajax({
                  type: "POST",
                  url: url,
                  data: { name: obj.src, _token: _token },
                  dataType: "json",
                  success: function(data) {
                    $("#" + file.id)
                      .find("img.showUploadImg")
                      .attr("src", data.data.src);
                    $("#" + file.id)
                      .find("img.showUploadImg")
                      .addClass("completed");
                    $("#" + file.id)
                      .find("b.showPercentage")
                      .hide();
                    // var input_arr = '<input type="hidden" name="'+name_prefix+input_name+images_key+'['+file.id+']" id="uploaded_image_'+file.id+'" value="'+encodeURI(obj.result)+'"  class="'+name_prefix+input_id+'_images" />';
                    // $(input).after(input_arr);
                  },
                });
              },
              Error: function(up, err) {
                console.log("Error #" + err.code + ": " + err.message);
              },
            },
          });
          // clr
          uploader.init();
          input.attr("data-active", "true");
        }
      });
  }
}

function setDeleteUploadFile(file, dot, storage, input, ui, is_array) {
  var del_input =
    '<input type="hidden" name="del_image_' +
    input +
    '[]" value="' +
    storage +
    "/" +
    file +
    "." +
    dot +
    '"/>';
  $("form").append(del_input);
  $("#" + file).remove();
}

function setSaveMultifile(frm) {
  event.preventDefault();
  var frm_data = new FormData($("#master_multifile_frm")[0]);
  $.ajax({
    url: "/master/multifiles/save",
    type: "POST",
    contentType: false,
    data: frm_data,
    processData: false,
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    success: function(resp) {
      if (resp.success) {
        noti("success", resp.msg);
        window.location.href = "/admin/master/multifiles/";
      } else {
        noti("error", resp.msg);
      }
    },
  });
}

function slug(str) {
  str = str
    .replace(/[`~!@#$%^&*()_\-+=\[\]{};:'"\\|\/,.<>?\s]/g, " ")
    .toLowerCase();
  str = str.replace(/^\s+|\s+$/gm, "");
  str = str.replace(/\s+/g, "-");
  return str;
}

DeleteImage = function(db, column, id, type) {
  bootbox.confirm({
    message: "ยืนยันการลบ? <br> เมื่อดำเนินการแล้วจะไม่สามารถย้อนกลับได้!",
    buttons: {
      confirm: {
        label: "ยืนยัน",
        className: "btn-dark",
      },
      cancel: {
        label: "ยกเลิก",
        className: "btn-default",
      },
    },
    callback: function(result) {
      if (result) {
        let _token = $('meta[name="csrf-token"]').attr("content");

        $.ajax({
          url: "/admin/mwz/delete-image",
          type: "POST",
          data: {
            db: db,
            column: column,
            id: id,
            type: type,
            _token: _token,
          },
          success: function(resp) {
            if (resp.success) {
              noti("success", resp.msg);
              JsReload(1500);
            } else {
              noti("error", resp.msg);
              JsReload(1500);
            }
          },
        });
      }
    },
  });
};

/* ----------------------- Customs ------------------------*/
// input only number and . point
InputValidateString = function(evt) {
  var theEvent = evt || window.event;

  if (theEvent.type === "paste") {
    key = event.clipboardData.getData("text/plain");
  } else {
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode(key);
  }

  var regex = /[0-9]|\./;
  if (!regex.test(key)) {
    theEvent.returnValue = false;
    if (theEvent.preventDefault) theEvent.preventDefault();
  }
};

///* ----------------------- tab init active  ------------------------*/
set_content_tab_init = function() {
  console.log('set_content_tab_init') ;
  if ($(".tab_wrapper").length > 0) {
    $(".tab_wrapper").each(function() {
      var ele_id = $(this).attr("id");
      var mode = $(this).data("mode");
      var $this = $(this);
      var activeTab = localStorage.getItem(ele_id + "_activeTab");
        
      console.log('set_content_tab_init:' + mode) ;
      if(mode=='edit'){
        if (activeTab) {
          var active = parseInt(activeTab) + 1;
          $(this).champ({ active_tab: active });
        } else {
          $(this).champ();
        }
      }else{
         $(this).champ();
      }
      
      // store last active tab
      $(this)
      .find(".tab_list li")
      .each(function() {
        $(this).bind("click", function() {
          localStorage.setItem(ele_id + "_activeTab", $(this).index());
        });
      });


    });
  }
};

set_nav_tab_init = function() {
  if ($(".nav.panel-tabs").length > 0) {
    $(".nav.panel-tabs").each(function() {
      var ele_id = $(this).attr("id");
      var mode = $(this).data("mode");
      console.log('set_nav_tab_init:' + mode) ;
      // if(mode=='edit'){
        $('a[data-toggle="tab"]').on("show.bs.tab", function(e) {
          localStorage.setItem(ele_id + "_activeTab", $(e.target).attr("href"));
        });
        var activeTab = localStorage.getItem(ele_id + "_activeTab");
        if (activeTab) {
          $("#" + ele_id + ' a[href="' + activeTab + '"]').tab("show");
        }
      // }
    });
  }
};

function delete_uploaded_image(ele, id) {
  var confirm_del_txt = $(ele).data("confirm-del-txt");
  var confirm_txt = $(ele).data("confirm-txt");
  var cancel_txt = $(ele).data("cancel-txt");
  var upload_click_to_upload_txt = $(ele).data("upload-click-to-upload-txt");
  // alert('delete_uploaded_image'+id);

  bootbox.confirm({
    message: confirm_del_txt,
    buttons: {
      confirm: {
        label: confirm_txt,
        className: "btn-success",
      },
      cancel: {
        label: cancel_txt,
        className: "btn-danger",
      },
    },
    callback: function(result) {
      if (result) {
        $(ele)
          .parents("div.image-upload")
          .find(".upload_show_img_container")
          .html(
            '<div class="d-upload-image"><i class="ion-upload"></i><p><b>' +
              upload_click_to_upload_txt +
              "</b></p></div>"
          );
        $(ele).removeClass("display-block");
        $(ele).addClass("display-none");
        $("#upload_image" + id).val("");
        $("#upload_image" + id + "_del").val(1);
      }
    },
  });
}

function init_upload() {
  if ($(".image-upload").length > 0) {
    $(".image-upload").each(function() {
      var data_id = $(this).data("id");
      var upload_light_gallery = $(this).find(".upload_lightgallery");
      var file_input = $(this).find('input[type="file"]')[0];
      file_input.onchange = (evt) => {
        const [file] = file_input.files;
        var url = URL.createObjectURL(file);
        if (file) {
          $(".upload_image" + data_id + " .dz-message").html(
            `<img id="blah" class="img-upload" src="${URL.createObjectURL(
              file
            )}" alt="image not available" />`
          );
          $("#btn_delete" + data_id).removeClass("display-none");
          $("#btn_delete" + data_id).addClass("display-block");
        }
      };

      // $("#btn_delete" + data_id).click(function() {
      //   delete_uploaded_image($(this), 1);
      // });
      $("#btn_delete" + data_id).bind("click", function() {
        // alert('BIND CLICK'+data_id);
        delete_uploaded_image($(this), data_id);
      });

      if (upload_light_gallery.length > 0) {
        lightGallery(upload_light_gallery);
      }
    });
  }
}

function init_multi_upload(){
  var all_dropzone = {};
  $('.dropzone').each(function(){
    var this_dropzone = this ;
    var this_form = $(this).closest('form') ;
    var action = $(this).data('action') ;
    var acceptedFiles = $(this).data('accepted-files') ;
    var paramName = $(this).data('param-name') ;
    var maxFiles = $(this).data('max-file') ;
    var uploadMsg = $(this).data('upload-msg') ;
    var removeMsg = $(this).data('remove-msg') ;
    var maxFileMsg = $(this).data('max-file-msg') ;
    var thumbnailPath = $(this).data('thumbnail-path') ;
    var files_list = [];
    if($(this_dropzone).find('input.file_list').val()!=""){
        var files_list = JSON.parse($(this_dropzone).find('input.file_list').val()) ;
    }

    // console.log('init_mwz_multi_upload');
    // console.log(action);
    // console.log(paramName);

    var current_drop_zone = {} ;

    current_drop_zone['element']=$(this_dropzone).dropzone({ 
        url: action,
        previewTemplate: document.getElementById('preview-template').innerHTML,
        autoProcessQueue: false,
        acceptedFiles: acceptedFiles,
        addRemoveLinks: true,
        uploadMultiple: true,
        parallelUploads: 5,
        maxFiles: maxFiles,
        paramName: paramName,
        dictDefaultMessage: '<i class="ion-upload"></i> '+uploadMsg,
        dictRemoveFile: '<i class="fa fa-trash"></i> '+removeMsg,
        dictMaxFilesExceeded: maxFileMsg,
        init: function () {
        
            myDropZone = this;
            current_drop_zone['object'] = myDropZone ;
            var container = myDropZone.element ;
            $(container).attr('data-image-cnt',0);
            var file_removed = [];
            
            // add element to form 
            this.on("addedfile", function(file, responseText) {
                var cnt =  $(this_dropzone).find('div.dz-preview').length - 1 ;
                if(cnt<=maxFiles){
                    console.log(cnt+' - '+maxFiles);
                    $(file.previewElement).attr("id", 'file_'+cnt);
                    $(file.previewElement).find('input').each(function(){
                        var name = paramName+'_'+$(this).data('name')+"["+cnt+"]" ;
                        var id = paramName+'_'+$(this).data('name')+"_"+cnt ;
                        $(this).attr("name",name);
                        $(this).attr("id",id) ;
                        if(typeof file[$(this).data('name')] !='undefined'){
                            $(this).val(file[$(this).data('name')]) ;
                        }
                    });
                }else{
                    this.removeFile(file);
                }
            });

            this.on("maxfilesexceeded", function(file, responseText) {
               this.removeFile(file);
            });
            
            // add element to form 
            this.on('sendingmultiple', function (data, xhr, formData) {
                let $form = $(this_form).serializeArray();
                for (var i = 0; i < $form.length; i++) {
                    formData.append($form[i].name,$form[i].value);
                }
            });

            this.on("removedfile", function(file) {
                // console.log('removedfile');
                // console.log(file);
                // console.log(responseText);
                if(file.complete){
                  file_removed.push(file.image) ;
                }else{
                  file_removed.push(file.name) ;
                }
                
                $(this_dropzone).find('input.file_removed').val(JSON.stringify(file_removed));

            });

            this.on("completemultiple", function(file, responseText) {
              console.log('completemultiple');
              // console.log(file);
              // console.log(responseText);
            });

            this.on("queuecomplete", function(file, responseText) {
              console.log('queuecomplete');
              // console.log(file);
              // console.log(responseText);
            });

            // init file
            if(files_list.length>0){
                $.each(files_list, function( index, mockFile ) {
                    var cnt =  $(this_dropzone).find('div.dz-preview').length ;
                    if(cnt<=maxFiles){   
                        // console.log(cnt+' - '+maxFiles); 
                        myDropZone.emit("addedfile", mockFile);
                        myDropZone.emit("thumbnail", mockFile, thumbnailPath+mockFile.image);
                        myDropZone.emit("complete", mockFile);
                    }
                });       
            }
        },      
    });

    $(this_dropzone).sortable({
        items: '.dz-preview',
        cursor: 'move',
        opacity: 0.5,
        containment: 'parent',
        distance: 20,
        tolerance: 'pointer',
        stop: function () {
            var queue = myDropZone.files;
            newQueue = [];
            $('.dropzone .dz-preview .dz-filename [data-dz-name]').each(function (count, el) {
                var name = el.innerHTML;
                queue.forEach(function (file) {
                    if (file.name === name) {
                        newQueue.push(file);
                    }
                });
            });
            myDropZone.files = newQueue;  


            // resort file in list
            var file_sort = [];
            $(this_dropzone).find('div.dz-preview').each(function(index){
              
                var img = {};
                img["complete"]= $(this).hasClass('dz-complete');

                if(img["complete"]){
                  var name =$(this).find(".dz-image").find('img').attr('src');
                }else{
                  var name =$(this).find("span[data-dz-name]").html();
                }
                
                img["image"] = name ;
                img["index"] = index ;
                
                $(this).find('input').each(function(){
                    img[$(this).data('name')] =$(this).val();
                });
                file_sort.push(img);
            });
            $(this_dropzone).find('input.file_list').val(JSON.stringify(file_sort));
        }
    });

    MULTI_UPLOAD[paramName] = current_drop_zone ;
  }); 
}

multi_upload_process = function(upload_element,parent_resp){
    // update file list before send
    console.log('multi_upload_process');
    console.log(upload_element) ;

    var myDropZone = upload_element['element'] ;
    var this_dropzone = upload_element['object']


    var file_sort = [];
    $(myDropZone).find('div.dz-preview').each(function(index){
      
        var img = {};
        img["complete"]= $(this).hasClass('dz-complete');

        if(img["complete"]){
          var name =$(this).find(".dz-image").find('img').attr('src');
        }else{
          var name =$(this).find("span[data-dz-name]").html();
        }
        
        img["image"] = name ;
        img["index"] = index ;
        
        $(this).find('input').each(function(){
            img[$(this).data('name')] =$(this).val();
        });
        file_sort.push(img);
        
    });
    
    console.log(file_sort) ;
    $(myDropZone).find('input.file_list').val(JSON.stringify(file_sort));

    // upload file 
    if (this_dropzone.getQueuedFiles().length > 0){
      this_dropzone.processQueue();  
    }else{
      multiFileUploadCallBack({},parent_resp);
    } 

    // upload finish
    this_dropzone.on("successmultiple", function(file, resp) {
      console.log('successmultiple');
      console.log(file);
      console.log(resp);
      if($.isFunction(multiFileUploadCallBack) ){
        console.log('call back multiFileUploadCallBack');
        multiFileUploadCallBack(file,resp);
      }else{
        console.log('not call back multiFileUploadCallBack');
      }
    });
}

generateMatadata = function (force = 1, langs = ['th', 'en'], slug_field = 'name', title_field = 'name', keywords_field = 'name', desc_field = 'desc'){
  $.each(langs,function(index,lang){
    if(force){
      var data_slug = slug($('#' + slug_field + '_' + lang).val());
      $('#metadata_' + lang + '_slug').val(data_slug);
      $('#metadata_' + lang + '_title').val($('#' + title_field + '_' + lang).val());
      $('#metadata_' + lang + '_keywords').val($('#' + keywords_field + '_' + lang).val());
      $('#metadata_' + lang + '_description').val($('#' + title_field + '_' + lang).val() + ' ' + $('#' + desc_field + '_' + lang).val());
    }else{
      if ($('#metadata_' + lang + '_slug').val()==''){
        var data_slug = slug($('#' + slug_field + '_' + lang).val());
        $('#metadata_' + lang + '_slug').val(data_slug);
      }
      if ($('#metadata_' + lang + '_title').val() == '') {
        $('#metadata_' + lang + '_title').val($('#' + title_field + '_' + lang).val());
      }
      if ($('#metadata_' + lang + '_keywords').val() == '') {
        $('#metadata_' + lang + '_keywords').val($('#' + keywords_field + '_' + lang).val());
      }
      if ($('#metadata_' + lang + '_description').val() == '') {
        $('#metadata_' + lang + '_description').val($('#' + title_field + '_' + lang).val() + ' ' + $('#' + desc_field + '_' + lang).val());
      }
    }
  
  }) ;



}

init_filebrowse = function(){
  if ($(".file-browse").length > 0) {
    $(".file-browse").each(function () {
      $(this).bind('click',function(){
        var url = $(this).data('url');
        var target_id = $(this).data('target-id');
        var preview_id = $(this).data('preview-id');
       
        $('<div id="' + target_id + '_finder" /></div>').insertAfter(this);
        dialog = $('<div id="' + target_id + '_finder"  />').dialogelfinder({
          customData: {
            _token: $('meta[name="csrf-token"]').attr("content"),
          },
          url: url,
          width: "900",
          height: "450",
          commandsOptions: {
            getfile: { oncomplete: 'destroy' }
          },
          getFileCallback: function (file) {
            console.log(file);
            $('#' + target_id).val(file.url);
            if (typeof preview_id != 'undefined' && preview_id != '') {
              $('#' + preview_id).attr('src', file.url);
            }
          } // pass callback to file manager
        });

      });
     
    });
  }
}

set_content_tab_init = function () {
  console.log('set_content_tab_init');
  if ($(".tab_wrapper").length > 0) {
    $(".tab_wrapper").each(function () {
      var ele_id = $(this).attr("id");
      var mode = $(this).data("mode");
      var $this = $(this);
      var activeTab = localStorage.getItem(ele_id + "_activeTab");

      console.log('set_content_tab_init:' + mode);
      if (mode == 'edit') {
        if (activeTab) {
          var active = parseInt(activeTab) + 1;
          $(this).champ({ active_tab: active });
        } else {
          $(this).champ();
        }
      } else {
        $(this).champ();
      }

      // store last active tab
      $(this)
        .find(".tab_list li")
        .each(function () {
          $(this).bind("click", function () {
            localStorage.setItem(ele_id + "_activeTab", $(this).index());
          });
        });


    });
  }
};

set_nav_tab_init = function () {
  if ($(".nav.panel-tabs").length > 0) {
    $(".nav.panel-tabs").each(function () {
      var ele_id = $(this).attr("id");
      var mode = $(this).data("mode");
      console.log('set_nav_tab_init:' + mode);
      // if(mode=='edit'){
      $('a[data-toggle="tab"]').on("show.bs.tab", function (e) {
        localStorage.setItem(ele_id + "_activeTab", $(e.target).attr("href"));
      });
      var activeTab = localStorage.getItem(ele_id + "_activeTab");
      if (activeTab) {
        $("#" + ele_id + ' a[href="' + activeTab + '"]').tab("show");
      }
      // }
    });
  }
};

jQuery.validator.addMethod("lettersonly", function (value, element) {
  return this.optional(element) || /^[a-zA-Zก-๏]+$/i.test(value);
}, "ต้องเป็นอักษรเท่านั้น"); 


//======================= time ago ======================= //
const MONTH_NAMES = [
  'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
  'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
];


function getFormattedDate(date, prefomattedDate = false, hideYear = false) {
  const day = date.getDate();
  const month = MONTH_NAMES[date.getMonth()];
  const year = date.getFullYear();
  const hours = date.getHours();
  let minutes = date.getMinutes();

  if (minutes < 10) {
    // Adding leading zero to minutes
    minutes = `0${minutes}`;
  }

  if (prefomattedDate) {
    // Today at 10:20
    // Yesterday at 10:20
    return `${prefomattedDate} เวลา ${hours}:${minutes}`;
  }

  if (hideYear) {
    // 10. January at 10:20
    return `${day}. ${month} เวลา ${hours}:${minutes}`;
  }

  // 10. January 2017. at 10:20
  return `${day}. ${month} ${year}. ${hours}:${minutes}`;
}

// --- Main function
function timeAgo(dateParam) {
  if (!dateParam) {
    return null;
  }

  const date = typeof dateParam === 'object' ? dateParam : new Date(dateParam);
  const DAY_IN_MS = 86400000; // 24 * 60 * 60 * 1000
  const today = new Date();
  const yesterday = new Date(today - DAY_IN_MS);
  const seconds = Math.round((today - date) / 1000);
  const minutes = Math.round(seconds / 60);
  const isToday = today.toDateString() === date.toDateString();
  const isYesterday = yesterday.toDateString() === date.toDateString();
  const isThisYear = today.getFullYear() === date.getFullYear();


  if (seconds < 5) {
    return 'now';
  } else if (seconds < 60) {
    return `${seconds}  วินาที`;
  } else if (seconds < 90) {
    return 'นาทีที่แล้ว';
  } else if (minutes < 60) {
    return `${minutes} นาที่`;
  } else if (isToday) {
    return getFormattedDate(date, 'Today'); // Today at 10:20
  } else if (isYesterday) {
    return getFormattedDate(date, 'Yesterday'); // Yesterday at 10:20
  } else if (isThisYear) {
    return getFormattedDate(date, false, true); // 10. January at 10:20
  }

  return getFormattedDate(date); // 10. January 2017. at 10:20
}

show_time_ago = function (date) {
  return timeAgo(date);
}

goCustomerDetail = function(customer_id){
  window.location = "/admin/customer/edit/" + customer_id;
}