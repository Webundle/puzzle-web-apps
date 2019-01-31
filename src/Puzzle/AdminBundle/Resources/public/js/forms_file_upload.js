$(function() {
    altair_form_file_upload.init(suffix = "", modal_to_close = "");
});

var altair_form_file_upload = {
    init: function(suffix) {
        
        if (suffix != "") {
            suffix = '_' + suffix;
        }else {
             suffix = '_' + $("#file_type").val();
        }

        var file_upload_context = $("#file_upload_context");
        var file_upload_select = $("#file_upload_select");
        var file_upload_drop = $("#file_upload_drop");
        var file_filters = $("#file_filters");
        var progressbar = $("#file_upload_progressbar");
        var target_container = $("#target_container" + suffix);
        var target_element = $("#target" + suffix);
        var files_to_add = $("#files_to_add" + suffix);
        var url = Routing.generate('admin_media_file_upload', {'context': file_upload_context.val()});

        if(file_upload_select.attr('multiple')){
            var data =  files_to_add.val();
        }else{
            var data =  "";
        }
        
        var bar         = progressbar.find('.uk-progress-bar'),
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

                    var obj = JSON.parse(response);
                    // Show picture
                    target_container.removeClass("uk-hidden");
                    target_container.addClass("uk-display-block");
                    console.log(target_element);
                    target_element.attr('src', obj.url);
                    $('#item-count-container').removeClass('uk-hidden');
                    $('#item-count').html(obj.url.split(',').length);
                    // Hide modal
                    UIkit.modal("#choose_files_modal").hide();
                    // Save picture value
                    files_to_add.val(data);
                    // Reload page automatically
                    if ($("#refresh-auto").val() == 1) {
                        window.location.assign($("#refresh-url").val());
                    }
                }
            };

        var select = UIkit.uploadSelect(file_upload_select, settings),
            drop   = UIkit.uploadDrop(file_upload_drop, settings);
    },
    choose_files_modal: function (type = "file", context = "media", selection_type = "") {
        var url = Routing.generate('admin_media_file_list', {
            'context': context,
            'type': type,
            'target': 'modal'
        });
        
        url = selection_type == "multiple-select" ? url + "&multiple_select=1" : url;
        
        /*$("#choose_files_modal_dialog").html("<div class=\"uk-text-center uk-margin-top uk-margin-bottom\"><i class=\"fa fa-spin fa-spinner fa-fw fa-5x\"></i></div>");*/
        $("#choose_files_modal_dialog").html('<div class="uk-text-center"><div class="md-preloader"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="96" width="96" viewBox="0 0 75 75"><circle cx="37.5" cy="37.5" r="33.5" stroke-width="4"></circle></svg></div></div>');
        
        $.ajax({
            url: url,
            async: false, // Synchrone mode
            success: function(response) {
                $("#choose_files_modal_dialog").html(data);
            }
        });
    },
    select_files_from_media: function(suffix = "", source = "") {

        suffix = suffix == "" ? "" : suffix = '_' + suffix;
        
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
                UIkit.modal("#choose_files_modal").hide();
                /*UIkit.notify({
                    message: "<i class=\"material-icons\">&#xE86C;</i>",
                    pos: 'bottom-center'
                });*/
            }
        } 
    }
};

// Load media
$("#load_media").click(function(){
    $("#fromMedia").html('<div class="uk-text-center"><div class="md-preloader"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="96" width="96" viewBox="0 0 75 75"><circle cx="37.5" cy="37.5" r="33.5" stroke-width="4"></circle></svg></div></div>');

    var url = Routing.generate('admin_media_file_browse', {
        'type': $("#file_type").val(), 
        'context': $("#file_upload_context").val(), 
        'multiple_select': $("#enable_mutiple_select").val(), 
        'target': 'modal'
    });
    
    $.get(url, function(response) {
        $('#fromMedia').html(response);
    });
});