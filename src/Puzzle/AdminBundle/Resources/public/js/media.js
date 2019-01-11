altair_form_file_upload.init("file");
altair_forms.init();
altair_md.init();

$('body').on('click', '#add_files_to_folder', function(e){
    e.preventDefault();
    var files = [];
        url = $(this).attr('href');

     $("div.icheckbox_md.checked > input").each(function(){
        var id = $(this).attr("id");
        // files.push($("#item_" + id).attr('src'));
        files.push(id);
    });

    if (files.length < 1) {
        $("#addFilesToFolder").hide();
        UIkit.modal.alert('Aucun fichier sélectionné !');
    }else {
        $.post(url, {files_to_add: files}, function(response){
            if(response.status == 1){
                $("#addFilesToFolder").hide();
                UIkit.modal.alert('Fichiers ajoutés !');
            }
        });
    }
});

$('body').on('click', '#compress-folder', function(e){
    e.preventDefault();
    $.get($(this).attr('href'), function(response){
        if (response.status == 1) {
            window.open(response.target,'_blank');
        }
    });
});

$('body').on('click', '#toggle-source', function(e){
    e.preventDefault();
    
    if ($("#source").val() == "local") {
        $('.alternate').removeClass('uk-hidden');
        $("#local").addClass('uk-hidden');
        $("#source").val('remote');
    }else {
        $('.alternate').addClass('uk-hidden');
        $("#source").val('local');
        $("#local").removeClass('uk-hidden');
    }
});


// Shared agenda
$('body').on('change', '#filter', function(e){
    if ($(this).val() == "customize"){
        $("#allowed-extensions-container").removeClass('uk-hidden');
        $("#filter_viewer").html('');
        $("#filter_viewer").addClass('uk-hidden');
    }else {
        $("#allowed-extensions-container").addClass('uk-hidden');
        $("#filter_viewer").html($(this).val().replace('|', ','));
        $("#filter_viewer").removeClass('uk-hidden');
    }
});


$("body").on('mouseenter', '.toggleable', togglize);
$("body").on('mouseleave', '.toggleable', untogglize);