// Functions 
function togglize() {
    $(".toggleable-btn").addClass('uk-hidden');
    $("#" + $(this).attr('id') + " .toggleable-btn").removeClass('uk-hidden');
}

function untogglize() {
    $("#" + $(this).attr('id') + " .toggleable-btn").addClass('uk-hidden');
}

// Show modal
$('body').on('click', '.show-modal', function( event ) {
    event.preventDefault();
    var groupId = $(this).attr('id'),
        url = $(this).attr("href");

    var modal = UIkit.modal("#showModal");
    modal.show();
    
    $("#showModal").html('<div class="uk-text-center" style="margin-top:200px"><div class="md-preloader"><svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="96" width="96" viewBox="0 0 75 75"><circle cx="37.5" cy="37.5" r="33.5" stroke-width="4"></circle></svg></div></div>');
        
    $.get( url).done(function( data ) {
        $("#showModal").html(data);
    });
})

// Remove item
$('body').on('click', '.remove-item', function(e){
    e.preventDefault();
    var url = $(this).data('route'),
        text = $(this).data('text'),
        label = $(this).data('label');

    UIkit.modal('#remove_item_modal').show();

    $("#remove_item_url").attr('href', url);
    console.log(text);
    if (text != undefined) {
        $('#remove_item_text').html(text);
    }else {
        $("#remove_item_label").html(label);
    }
}) 

// Mutiple select or unselect
$("body").on('click', '#toggle-check', function(e){
    e.preventDefault();

    if (! $(this).hasClass("all-checked")) {
        var isCheck = 0;
        $(this).addClass("all-checked");
        $(this).find('i:first').html('&#xE834;');
        $("#toggle-items-checked-count").val($("#toggle-items-count").val());
    }else {
        var isCheck = 1;
        $(this).removeClass("all-checked");
        $(this).find('i:first').html('&#xE835;');
        $("#toggle-items-checked-count").val(0);
    }

    $(".toggleable-btn-select").each(function(){
        if(isCheck == 0){
            $(this).addClass("checked");
            $(this).removeClass('uk-hidden');
            $(this).find('i:first').html('&#xE834;');
            // $("#toggle-check-text").html("ANNULER");
            $(".toggle-action").removeClass('uk-hidden');
            // Detach mouseenter and mouseover events
            $("body").off('mouseenter', '.toggleable', togglize);
            $("body").off('mouseleave', '.toggleable', untogglize);
        }else{
            $(this).removeClass("checked");
            $(this).addClass('uk-hidden');
            $(this).find('i:first').html('&#xE835;');
            // $("#toggle-check-text").html("TOUT SELECTIONNER");
            $(".toggle-action").addClass('uk-hidden');
            // Attach mouseenter and mouseover events
            $("body").on('mouseenter', '.toggleable', togglize);
            $("body").on('mouseleave', '.toggleable', untogglize);
        }
    });
    console.log();
});

// Choose files
$('body').on('click', '.choose_files_btn', function(){
    var context = $(this).data('context');
    var type = $(this).data('type');
    var multiple = $(this).data('multiple');

    altair_form_file_upload.choose_files_modal(type, context, multiple);
})

// Mutiple select or unselect
$('body').on('click', ".toggleable-btn-select", function(e){
    e.preventDefault();
    var toggleItemCheckedCount = parseInt($("#toggle-items-checked-count").val());
    
    if (! $(this).hasClass("checked")) {
        var isCheck = 0;
        $(this).addClass("checked");
    }else {
        var isCheck = 1;
        $(this).removeClass("checked");
    }

    if (isCheck == 0) {
        toggleItemCheckedCount++;
        $(this).find('i:first').html('&#xE834;');
        $("#toggle-items-checked-count").val(toggleItemCheckedCount);
        if ($("#toggle-items-count").val() == toggleItemCheckedCount) {
            $("#toggle-check").addClass("all-checked");
            $("#toggle-check-icon").html('&#xE834;');
            $("#toggle-check-text").html("ANNULER");
        }
        
        $(".toggle-action").removeClass('uk-hidden');
        $(".toggleable-btn-select").removeClass('uk-hidden');
        $("body").off('mouseenter', '.toggleable', togglize);
        $("body").off('mouseleave', '.toggleable', untogglize);
    }else {
        toggleItemCheckedCount--
        $(this).find('i:first').html('&#xE835;');
        $("#toggle-items-checked-count").val(toggleItemCheckedCount);
        $("#toggle-check").removeClass("all-checked");
        $("#toggle-check-icon").html('&#xE835;');
        if (toggleItemCheckedCount <= 0) {
            $("#toggle-check-text").html("TOUT SELECTIONNER");
            $(".toggle-action").addClass('uk-hidden');
            $("body").on('mouseenter', '.toggleable', togglize);
            $("body").on('mouseleave', '.toggleable', untogglize);
        }else {
            $("#toggle-check-text").html("ANNULER");
        }
    }
    
    var ids = [];
    $(".toggleable-btn-select.checked > i").each(function() {
        ids.push($(this).attr("id").replace('toggle-item-', ''));
    });
    
    $("#toggle-items-checked-id").val(ids.join(','));
});


$('body').on('click', '#remove_list_btn', function(e){
    e.preventDefault();
    var url = $(this).attr('href');
    var ids = [];
    $(".toggleable-btn-select.checked > i").each(function(){
        ids.push($(this).attr("id").replace('toggle-item-', ''));
    });

    $.post( url , { ids: ids.join(",")}, function( data ) {
        if(data.status == 1){
            for (var i = 0; i < ids.length; i++) {
                $("#grid_file_" + ids[i]).fadeOut("fast");
            }
            $("#remove_list_modal").hide();
            // UIkit.modal.alert("Fichiers supprimés");
            window.location.reload(true);
        }
    });
});


$('body').on('click', '.remove-row', function(e){
   e.preventDefault();
    var url = $(this).attr('href');
    
    $.get(url, function(response){
        $(this).closest('tr').fadeOut();
    }).fail(function(e){
        alert(e.responseText);
    });
});


$('body').on('click', '#show_more', function(e){
    e.preventDefault();
    $(this).addClass('uk-hidden');
    $(".togglable").each(function(){
        $(this).removeClass("uk-hidden");
    })
});

$('body').on('click', '.remove-file', function(e) {
    var id = $(this).attr('id'),
        type = $(this).data('type');

    // Get picture path to remove
    var picture = $("#" + type + '-' + id).attr('src').split('?')[0];
    // Get all pictures converted in array
    var pictures = $("#files_to_add_" + type).val().split(',');
    // Get picture index in pictures array
    var index = pictures.indexOf(picture);
    // Remove picture to pictures
    if (index !== -1) pictures.splice(index, 1);
    // Update filesToPictrue value
    $("#files_to_add_" + type).val(pictures.join(','));
    var nextLiId = parseInt(id) + 1;
    $("#li-" + nextLiId).removeClass('uk-grid-margin');
    // Remove DOM Element
    $("#li-" + id).fadeOut();
});

// Datetime Picker
$(".datepicker").kendoDateTimePicker({
    format: "dd-MM-yyyy HH:mm",
    // value: $(this).value()
});

