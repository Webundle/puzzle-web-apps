// $(function() {
//     altair_form_file_upload.init(suffix = "", modal_to_close = "");
// });

var altair_form_file_upload = {
    init: function(suffix) {
        
        if (suffix != "") {
            suffix = '_' + suffix;
        }else {
             suffix = '_' + $("#file_type").val();
        }
        
        var file_upload_context = $("#file_upload_context" + suffix);
        var file_upload_select = $("#file_upload_select" + suffix);
        var file_upload_drop = $("#file_upload_drop" + suffix);
        var file_filters = $("#file_filters" + suffix);
        var progressbar = $("#file_upload_progressbar" + suffix);
        var target_container = $("#target_container" + suffix);
        var target_element = $("#target" + suffix);
        var files_to_add = $("#files_to_add" + suffix);
        var url = Routing.generate('admin_media_file_upload', {'context': file_upload_context.val()});

        if (file_upload_select.attr('multiple')) {
            var data =  files_to_add.val();
        }else{
            var data =  "";
        }
        
        var bar         = $('#progress_bar' + suffix),
            settings    = {
                action: url, 
                allow : file_filters.val(),
                loadstart: function() {
                    bar.css("width", "0%").text("0%");
                    progressbar.removeClass("uk-hidden");
                },
                progress: function(percent) {
                    percent = Math.ceil(percent);
                    bar.css("width", percent+"%").text(percent+"%");
                },
                complete: function(response,xhr) {
                    var obj = JSON.parse(response);

                    if (data == "") {
                        data = obj.id;
                    }else{
                        data = data + ',' + obj.id;
                    }
                },
                allcomplete: function(response,xhr) {
                    bar.css("width", "100%").text("100%");
                    setTimeout(function(){
                        progressbar.addClass("uk-hidden");
                    }, 250);
                    /*setTimeout(function() {
                        UIkit.notify({
                            message: "Téléchargement terminé",
                            pos: 'top-right'
                        });
                    },280);*/


                    // Save picture value
                    files_to_add.val(data);
                    
                    // Show picture
                    var obj = JSON.parse(response);
                    var urls = obj.url.split(',');

                    // Show preview
                    target_container.removeClass("uk-hidden");
                    target_container.addClass("uk-display-block");
                    target_element.attr('src', urls[0]);

                    // Count elements
                    $('#item_count_container' + suffix).removeClass('uk-hidden');
                    $('#item_count' + suffix).html(urls.length);

                    // Hide modal
                    UIkit.modal("#choose_files_modal" + suffix).hide();

                    // Reload page automatically
                    if ($("#refresh-auto").val() == 1) {
                        window.location.assign($("#refresh-url").val());
                    }
                }
            };

        var select = UIkit.uploadSelect(file_upload_select, settings),
            drop   = UIkit.uploadDrop(file_upload_drop, settings);
    },
    choose_file_modal: function (type = "file", context = "media", selection_type = "") {
        var url = Routing.generate('admin_media_file_list', {
            'context': context,
            'type': type,
            'target': 'modal'
        });
        
        url = selection_type == "multiple-select" ? url + "&multiple_select=1" : url;
        
        $("#choose_file_modal_dialog_" + type).html('<div class="uk-text-center"><div class="md-preloader"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="96" width="96" viewBox="0 0 75 75"><circle cx="37.5" cy="37.5" r="33.5" stroke-width="4"></circle></svg></div></div>');
        
        $.ajax({
            url: url,
            async: false, // Synchrone mode
            success: function(response) {
                $("#choose_file_modal_dialog_" + type).html(data);
            }
        });
    },
    select_files_from_media: function(suffix = "", source = "") {

        if (suffix != "") {
            suffix = '_' + suffix;
        }else {
             suffix = '_' + $("#file_type").val();
        }
        
        var target_container = $("#target_container" + suffix),
            target_element = $("#target" + suffix),
            files_to_add = $("#files_to_add" + suffix),
            paths = [];

        if (source != "") {
            paths.push(source);
        }else {
            if (files_to_add.val() != "") {
                paths.push(files_to_add.val().split(","));
            }

            $("div.icheckbox_md.checked > input").each(function() {
                var id = $(this).attr("id");
                paths.push($("#item_" + id).attr('src'));
            });
        }

        if (paths[0] != "#") {
            target_element.attr('src',paths[0]);
            target_container.removeClass('uk-hidden');
            files_to_add.val(paths.join(","));

            if($("#refresh-auto").val() == 1){
                window.location.assign($("#refresh-url").val());
            }else{
                UIkit.modal("#choose_files_modal" + suffix).hide();
            }
        } 
    }
};

// Load media
$(".load_media").click(function(){
    var type = $(this).data('type');
    
    $("#fromMedia_" + type).html('<div class="uk-text-center"><div class="md-preloader"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="96" width="96" viewBox="0 0 75 75"><circle cx="37.5" cy="37.5" r="33.5" stroke-width="4"></circle></svg></div></div>');

    var url = Routing.generate('admin_media_file_browse', {
        'type': type, 
        'context': $("#file_upload_context_" + type).val(), 
        'multiple_select': $("#enable_mutiple_select_" + type).val(), 
        'target': 'modal'
    });
    
    $.get(url, function(response) {
        $('#fromMedia_' + type).html(response);
    });
});