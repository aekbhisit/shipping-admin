// init script
$(document).ready(function(){
    // init multi file upload
    initFileUpload() ;

    init_maps_picker();

    //init_select2();

    // int_core_province();

    int_core_zipcode();
});

// redirect to page
core_redirect=function(url){
    window.location.href = url ;
}
// form validation
core_frm_validate=function(selector,rules, messages){
    var valid_result = $(selector).validate( {
        rules: rules,
        messages: messages,
        errorPlacement: function ( error, element ) {
            error.addClass( "invalid-feedback" );
            if ( element.prop( "type" ) === "checkbox" ) {
                error.insertAfter( element.next( "label" ) );
            } else {
                error.insertAfter( element );
            }
        },
        errorElement: 'div',
        errorClass: 'invalid-feedback',
        highlight: function ( element, errorClass, validClass ) {
            $( element ).addClass( "is-invalid" ).removeClass( "is-valid" );
        },
        unhighlight: function (element, errorClass, validClass) {
            $( element ).addClass( "is-valid" ).removeClass( "is-invalid" );
        }
    } );
    console.log(valid_result);
}

// core_global_load
core_global_loading=function(show=1){
    if(show){
        $('#global-loader').fadeIn(100);
    }else{
        $('#global-loader').fadeOut(100);
    }
}

// notification
noti=function(type,msg) {
    console.log('core_noti'+type+" "+msg);
    switch(type){
        case 'success':
            return $.growl.notice({
            title: "success",
            message: msg
        });
        break;
        case 'warning':
            return $.growl.warning({
            title: "warning",
            message: msg
        });
        break;
        case 'error':
            return $.growl.error({
            title: "error",
            message: msg
        });
        break;
        default:
            return $.growl({
                title: "notice",
                message: msg
            });
        break;
    }
};

function elFinderBrowser (field_name, url, type, win) {
    tinymce.activeEditor.windowManager.open({
        file: '/admin/elfinder/tinymce4',// use an absolute path!
        title: 'File Management',
        width: 900,
        height: 450,
        resizable: 'yes'
        },{
            setUrl: function (url) {
            win.document.getElementById(field_name).value = url;
        }
    });
    return false;
}


(function($) {

    // init texteditor
    if($(".texteditor").length > 0){
        
        tinymce.init({
            selector:"textarea.texteditor",
            skin: "lightgray",
            themes: "modern",
            inline:false,
            height:250,
            mode:"textareas",
            relative_urls:true,
            extended_valid_elements:"i[class|style|title],iframe[src|frameborder|style|scrolling|class|width|height|name|align]",
            fontsize_formats:"8pt 10pt 11pt 12pt 13pt 14pt 15pt 16pt 17pt 18pt 19pt 20pt 21pt 22pt 23pt 24pt 25pt 26pt 28pt 30pt 32pt 34pt 36pt",
            // file_browser_callback:elFinderBrowser,
            plugins: [
                "advlist autoresize autolink lists link image charmap print preview anchor emoticons",  
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table contextmenu paste textcolor hr "
            ],
            autoresize_overflow_padding:5,
            autoresize_min_height:250,
            autoresize_max_height:500,
            extended_valid_elements:'i[class|style|title],span[class|style|title],a[accesskey|charset|class|contenteditable|contextmenu|coords|dir|download|draggable|dropzone|hidden|href|hreflang|id|lang|media|name|rel|rev|shape|spellcheck|style|tabindex|target|title|translate|type|onclick|onfocus|onblur],marquee',
            toolbar1:" insertfile bootstrap ",
            toolbar2:" undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media ",
            toolbar3:" forecolor backcolor | formatselect styleselect fontselect fontsizeselect | emoticons fontawesome "
        });

    }
    // init texteditor
    if($(".texteditor").length > 0){
        // Select2 by showing the search
        $('.select2-show-search').select2({
            minimumResultsForSearch: ''
        });
    }

    // init maps picker
    // alert($(".maps_picker").length) ;
    // if($(".maps_picker").length > 0){
    //     $(".core_maps_picker").each(function(){
    //         // alert(this);
    //         $(this).PlacePicker({
    //            btnClass:"btn btn-xs btn-default",
    //             key:"AIzaSyAEFLuCxU99kLoW2NB7_iQesxPuYaNSwiM",
    //             center: {lat: 17.6868, lng: 83.2185},
    //             success:function(data,address){
    //                 $(this).val(data.formatted_address);
    //             }
    //         });
    //     });
    // }

    
})(jQuery);



init_maps_picker=function(){
    $(".maps_picker").each(function(){
        var ele = $(this) ;
        console.log( $(ele).attr('class') ) ;
        $(this).PlacePicker({
           btnClass:"btn btn-xs btn-default",
            key:"AIzaSyASUQHNsNID4l1HkBuawQGqaJ9rtUU-HqI",
            center: {lat: 13.758036276095279, lng: 100.56483464997967}, 
            success:function(data,address){
                // console.log($(this).html());
                console.log(data.currentLocation);
                console.log(data.formatted_address);
                $(ele).val(data.formatted_address);
            }
        });
    });

}

init_select2=function(){
    if($('.select2-show-search').length>0){
       $('.select2-show-search').select2({
         minimumResultsForSearch: ''
       });
    }

    if($('.select2').length>0){
        $('.select2').select2({
         minimumResultsForSearch: Infinity
        });
    }

}

int_core_zipcode=function(){
    if($('.core-zipcode').length>0){
        // alert('zipcode');
        $(document).find('.core-zipcode').each(function(){
            var frm = $(this).closest('form');
            var city = $(frm).find('.core-city')  ;
            var district = $(frm).find('.core-district')  ;
            var province = $(frm).find('.core-province')  ;
            var zipcode = $(this) ;
            var address ;
            var init_province ;
            var init_city ;
            var init_district ;
            if($(zipcode).val().length<5){
                $(zipcode).keyup(function(){
                    if($(zipcode).val().length==5&&$.isNumeric($(zipcode).val())){
                        $.ajax({
                            url: "/core/get_address_by_zipcode/select2/"+$(zipcode).val(),
                            type:"GET",
                            dataType: 'json',
                            success:function(data){
                                console.log(data);
                                if(data.success==1){
                                    address=data.address;
                                    init_province = address.province[0];
                                    init_city=address.city[init_province.id][0];
                                    init_district=address.district[init_city.id][0];

                                    $(province).empty();
                                    $(province).select2({
                                        data: address.province,
                                        minimumResultsForSearch: '',
                                        placeholder: $(province).data('placeholder')
                                    });
                                    $(province).val(init_province.id).trigger("change");

                                    $(city).empty();
                                    $(city).select2({
                                        data: address.city[init_province.id],
                                        minimumResultsForSearch: '',
                                        placeholder: $(city).data('placeholder')
                                    });
                                    $(city).val(init_city.id).trigger("change");

                                    $(district).empty();
                                    $(district).select2({
                                        data: address.district[init_city.id],
                                        minimumResultsForSearch: '',
                                        placeholder: $(district).data('placeholder')
                                    }); 
                                    $(district).val(init_district.id).trigger("change");
                                }else{
                                    core_noti('error',data.msg);
                                    $(zipcode).val('');
                                    $(province).empty();
                                    $(city).empty();
                                    $(district).empty();
                                    return false ;
                                }
                            },
                        }).then(function(){
                            $(province).on('change', function() {
                                var province_id = this.value ;
                                init_city=address.city[province_id][0];
                                init_district=address.district[init_city.id][0];

                                $(city).empty();
                                $(city).select2({
                                    data: address.city[province_id],
                                    minimumResultsForSearch: '',
                                    placeholder: $(city).data('placeholder')
                                });
                                $(city).val(init_city.id).trigger("change");

                                $(district).empty();
                                $(district).select2({
                                    data: address.district[init_district.id],
                                    minimumResultsForSearch: '',
                                    placeholder: $(district).data('placeholder')
                                }); 
                                $(district).val(init_district.id).trigger("change");

                            });

                            $(city).on('change', function() {
                                var city_id = this.value ;
                                init_district=address.district[city_id][0];

                                $(district).empty();
                                $(district).select2({
                                    data: address.district[city_id],
                                    minimumResultsForSearch: '',
                                    placeholder: $(district).data('placeholder')
                                }); 
                                $(district).val(init_district.id).trigger("change");
                            });          
                        });
                    }
                });
            }else{
                if($(zipcode).val().length==5&&$.isNumeric($(zipcode).val())){

                    $.ajax({
                        url: "/core/get_address_by_zipcode/select2/"+$(zipcode).val(),
                        type:"GET",
                        dataType: 'json',
                        success:function(data){
                            console.log(data);
                            if(data.success==1){
                                address=data.address;
                                init_province=$(province).data('selected') ;
                                init_city=$(city).data('selected') ;
                                init_district=$(district).data('selected') ;

                                $(province).empty();
                                $(province).select2({
                                    data: address.province,
                                    minimumResultsForSearch: '',
                                    placeholder: $(province).data('placeholder')
                                });
                                // .select2().val("0").trigger("change");
                                $(province).val(init_province).trigger("change");

                                // $(city).empty();
                                $(city).select2({
                                    data: address.city[init_province],
                                    minimumResultsForSearch: '',
                                    placeholder: $(city).data('placeholder')
                                });
                                $(city).val(init_city).trigger("change");

                                // $(district).empty();
                                $(district).select2({
                                    data: address.district[init_city],
                                    minimumResultsForSearch: '',
                                    placeholder: $(district).data('placeholder')
                                }); 
                                $(district).val(init_district).trigger("change");
                            }else{
                                core_noti('error',data.msg);
                                $(zipcode).val('');
                                $(province).empty();
                                $(city).empty();
                                $(district).empty();
                                return false ;
                            }
                        },
                        }).then(function(){
                            $(province).on('change', function() {
                                var province_id = this.value ;
                                init_city=address.city[province_id][0];
                                init_district=address.district[init_city.id][0];

                                $(city).empty();
                                $(city).select2({
                                    data: address.city[province_id],
                                    minimumResultsForSearch: '',
                                    placeholder: $(city).data('placeholder')
                                });
                                $(city).val(init_city.id).trigger("change");

                                $(district).empty();
                                $(district).select2({
                                    data: address.district[init_district.id],
                                    minimumResultsForSearch: '',
                                    placeholder: $(district).data('placeholder')
                                }); 
                                $(district).val(init_district.id).trigger("change");

                            });

                            $(city).on('change', function() {
                                var city_id = this.value ;
                                init_district=address.district[city_id][0];

                                $(district).empty();
                                $(district).select2({
                                    data: address.district[city_id],
                                    minimumResultsForSearch: '',
                                    placeholder: $(district).data('placeholder')
                                }); 
                                $(district).val(init_district.id).trigger("change");
                            });          
                    });
                }
            }

        });
  
    }
}


int_core_province=function(){
    if($('.core-province').length>0){

        $(document).find('.core-province').each(function(){
            var frm = $(this).closest('from');
            var city = $(frm).find('.core-city')  ;
            var district = $(frm).find('.core-district')  ;
            var province = $(this) ;

            $.ajax({
                url: "/admin/core/get_province/select2",
                type:"GET",
                dataType: 'json',
                success:function(data){
                    $(province).select2({
                        data: data,
                        minimumResultsForSearch: '',
                        placeholder: $(province).data('placeholder')
                    });
                },
            }).then(function(){
                $(province).val($(province).data('selected')).trigger("change");
            });

        });

       
    }
}

function initFileUpload(){
    if($("input.fileUpload").length > 0){
        $(document).find('input.fileUpload').each(function(){
            if($(this).attr('data-active')!="true"){
                var str_to_boolean = new Array();
                str_to_boolean['false'] = false;
                str_to_boolean['true'] = true;
                var m_f_s = $(this).attr('data-maxfilesize'); 
                var a_f_u = $(this).attr('data-allowfile'); 
                var storage = $(this).attr('data-storage'); 
                var input = $(this);
                var input_id = $(this).attr('id'); 
                var input_name = $(this).attr('name'); 
                var data_multiple = str_to_boolean[$(this).attr('data-multiple')]; 
                var data_autostart = str_to_boolean[$(this).attr('data-autostart')]; 
                var data_target = $(this).attr('data-target'); 
                var data_domainid = $(this).attr('data-dmid');
                var data_browse = $(this).attr('data-browse');
                var data_array_name = $(this).attr('data-array');
                var images_key = "_images";
                var delete_key = "_delete";
                var name_prefix = '';
                var is_array = false;
                if(str_to_boolean[data_array_name]==true){
                    images_key = "[images]";
                    //delete_key = "[delete]";
                    name_prefix = 'array_';
                    is_array = true;
                }
                var uploaded_queue =  0;
                var file_uploaded  = [];
                var container =  '<div id="'+input_id+'_container" class="fileUploadContainer"><input name="'+input_id+delete_key+'" id="'+input_id+delete_key+'" type="hidden" value="" /><button type="button" class="btn btn-info" id="'+input_id+'_btn_upload">'+data_browse+'</button></div><div id="'+input_id+'_filelist"></div>';
                $(this).after(container);
                var uploader = new plupload.Uploader({
                    runtimes : 'html5,silverlight,html4',
                    browse_button : input_id+'_btn_upload', // you can pass in id...
                    multi_selection : data_multiple,  
                    unique_names : true,
                    container : document.getElementById('container'), // ... or DOM Element itself
                    url : '/admin/master/multifiles/upload',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    flash_swf_url : '/plupload/js/Moxie.swf',
                    silverlight_xap_url : '/plupload/js/Moxie.xap',
                    filters : {
                        max_file_size : m_f_s,
                        mime_types: [
                        {title : "Image files", extensions : a_f_u }
                        ]
                    },
                    init: {
                        PostInit: function() {
                            if(input.val() != ''){
                                var img_arr = input.val().split(",");
                                plupload.each(img_arr, function(path) { 
                                    file = path.replace(storage+"/", "");
                                    var file_dot = file.split('.');
                                    var chk_dot = (file_dot.length-1);
                                    var filename = file_dot[0];
                                    file_dot = file_dot[1];

                                    var src = '';
                                    var url = "{{ route('admin.master.multifiles.src') }}";
                                    var _token = $('meta[name="csrf-token"]').attr('content');
                                    $.ajax({
                                        type: 'POST',
                                        url: url,
                                        data: { name: path, _token: _token },
                                        dataType: "json",
                                        success: function(data) {
                                            document.getElementById(input_id+'_filelist').innerHTML += '<div id="' + filename + '" data-dot="'+file_dot+'" class="showUploadImageList" data-system="false"><img src="'+data.data.src+'" class="showUploadImg" id="img_' + filename + '" /><b class="showPercentage"></b><button class="btn btn-xs btn-danger btn-notification delete-image" id="btn_delete_'+filename+'" data-layout="top" data-type="confirm"  data-modal="true" type="button" onclick="setDeleteUploadFile(\''+filename+'\',\''+file_dot+'\',\''+storage+'\',\''+input_name+'\',this,'+is_array+');"><i class="icon-trash"></i></button><div style="clear:both;"></div><i class="filename"><div class="filename_block">' +(filename+'.'+file_dot) + '</div></i></div>';
                                        }
                                    });

                                    
                                });
                            }
                            if(data_autostart=='false'){
                                document.getElementById(input_id+'_btn_upload').onclick = function() {
                                    uploader.start();
                                    return false;
                                };
                            }
                        },
                        FilesAdded: function(up, files) {
                            if(data_multiple==false){
                                $(input).val('');
                                $('#'+input_name+'_filelist').find('div.showUploadImageList').each(function(index, element) {
                                
                                    var file_id = $(this).attr('id');
                                    var file_dot = $(this).attr('data-dot');
                               
                                    setDeleteUploadFile(file_id,file_dot,storage,input_name,$(this),is_array)
                                });
                            }
                            plupload.each(files, function(file) { 
                                var file_dot = file.name.split('.');
                                var chk_dot= (file_dot.length-1);
                                file_dot = file_dot[chk_dot];
                                document.getElementById(input_id+'_filelist').innerHTML += '<div id="' + file.id + '" data-dot="'+file_dot+'" class="showUploadImageList" data-system="false"><img src="/assets/images/admin/ajax-loader.gif" class="showUploadImg" id="img_' + file.id + '" /><b class="showPercentage"></b><button class="btn btn-xs btn-danger btn-notification delete-image" id="btn_delete_'+file.id+'" data-layout="top" data-type="confirm"  data-modal="true" type="button" onclick="setDeleteUploadFile(\''+file.id+'\',\''+file_dot+'\',\''+storage+'\',\''+input_name+'\',this,'+is_array+');"><i class="icon-trash"></i></button><div style="clear:both;"></div><i class="filename"><div class="filename_block">' +(file.name) + ' (' + plupload.formatSize(file.size) + ')</div></i></div>';
                            });
                            if(data_autostart=='true'||data_autostart===true){  
                                uploader.start();
                            }
                        },
                        UploadProgress: function(up, file) {
                            document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
                        },
                        UploadComplete:function(up, file){
                            $('#'+input_id+'_filelist').sortable({
                                placeholder: "sort-image-placeholder",
                                delay: 200
                            });
                            // auto sort file when uplaod complete
                            var sort_list = [];
                            $('#'+input_id+'_filelist').find('div.showUploadImageList').each(function(index, element) {
                                sort_list.push(storage + '/' + $(this).attr('id') + '.' + $(this).attr('data-dot')); 
                            });
                            $(input).val(sort_list.join(','));
                            // sort file by drag&drop
                            $( '#'+input_id+'_filelist').bind( "sortstop", function(event, ui) {
                                var sort_list = [];
                                $(this).find('div.showUploadImageList').each(function(index, element) {
                                    sort_list.push(storage + '/' + $(this).attr('id') + '.' + $(this).attr('data-dot')); 
                                });
                                $(input).val(sort_list.join(','));
                            });
                        },
                        FileUploaded:function(up, file, info){
                            var file_dot = file.name.split('.');
                            var chk_dot= (file_dot.length-1);
                            var file_name = file.id + file_dot;
                            var obj = JSON.parse(info.response);
                            // var src = storagePath + file_name;
                            var src = '';
                            var url = "{{ route('admin.master.multifiles.src') }}";
                            var _token = $('meta[name="csrf-token"]').attr('content');
                            $.ajax({
                                type: 'POST',
                                url: url,
                                data: { name: obj.src, _token: _token },
                                dataType: "json",
                                success: function(data) {
                                    $('#'+file.id).find('img.showUploadImg').attr('src',data.data.src);
                                    $('#'+file.id).find('img.showUploadImg').addClass('completed');
                                    $('#'+file.id).find('b.showPercentage').hide();
                                    // var input_arr = '<input type="hidden" name="'+name_prefix+input_name+images_key+'['+file.id+']" id="uploaded_image_'+file.id+'" value="'+encodeURI(obj.result)+'"  class="'+name_prefix+input_id+'_images" />';
                                    // $(input).after(input_arr);
                                }
                            });
                        },
                        Error: function(up, err) {
                            console.log("Error #" + err.code + ": " + err.message );
                        }
                    }
                });
                // clr
                uploader.init();
                input.attr("data-active","true");
            }
        });
    }
}

function setDeleteUploadFile(file,dot,storage,input,ui,is_array){
  
    var del_input = '<input type="hidden" name="del_image_'+input+'[]" value="'+storage+'/'+file+'.'+dot+'"/>';
    $('form').append(del_input);
    $('#'+file).remove();
    
}

function setSaveMultifile(frm){
    event.preventDefault();
    var frm_data = new FormData($('#master_multifile_frm')[0]);
    $.ajax({
        url: "/admin/master/multifiles/save" ,
        type:"POST",
        contentType: false,
        data: frm_data ,
        processData: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success:function(resp){
            if(resp.success){
                core_noti('success',resp.msg);
                window.location.href = '/admin/master/multifiles/';
            }else{
                core_noti('error',resp.msg);
            }
        },
    });
}

JsReload = function (time) {
    setInterval(function () {
        window.location.reload();
    }, time);
}

JsRedirect = function (url, time) {
    setInterval(function () {
        window.location.replace(url);
    }, time);
}
JsReloadTable = function (table) {
    $('#' + table).DataTable().ajax.reload(null, false);
}
DeleteImage = function (db, column, id, type) {
    bootbox.confirm({
        message: "ยืนยันการลบ? <br> เมื่อดำเนินการแล้วจะไม่สามารถย้อนกลับได้!",
        buttons: {
            confirm: {
                label: 'ยืนยัน',
                className: 'btn-dark'
            },
            cancel: {
                label: 'ยกเลิก',
                className: 'btn-default'
            }
        },
        callback: function (result) {
            if (result) {
                let _token = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: "/admin/core/delete-image",
                    type: "POST",
                    data: {
                        db: db,
                        column: column,
                        id: id,
                        type: type,
                        _token: _token
                    },
                    success: function (resp) {
                        if (resp.success) {
                            core_noti('success', resp.msg);
                            JsReload(1500);
                        } else {
                            core_noti('error', resp.msg);
                            JsReload(1500);
                        }
                    },
                });
            }
        }
    })
}



