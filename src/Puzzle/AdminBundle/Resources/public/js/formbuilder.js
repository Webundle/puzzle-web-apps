// Add element form
$('.add-element-link').on('click', function(e) {
    e.preventDefault();

    var $countElements = parseInt($("#count_elements").val());
    $countElements = $countElements > 0 ? $countElements + 1 : 1;

    addElementForm($countElements);
});

// Remove element form
$('.remove-element').click(function(e) {
    e.preventDefault();
    var url = $(this).attr('href'),
        declarationId = $(this).attr('id');

    $.post(url,{declaration_id: declarationId}, function(response) {
        if (response.status == "1") {
            $("#container_elements_" + declarationId).remove();
        }
    });
});

// Remove old element form
$(".remove_element_link").on('click', function(e) {
    e.preventDefault();
    removeElementForm($(this).attr('id'));
});

/**
 * Add element form
 * 
 * @param index
 */
function addElementForm(index) {
    var removeItemLink = $('.remove_element_link').html();
    var newForm = $("#prototype").val();
    $('.remove_element_link').html(removeItemLink);
    
    newForm = newForm.replace(/index/g, index);
    var newFormFull = $(newForm);
    var $newFormLi = $('<div class="uk-grid uk-margin-bottom uk-margin-small-top" id="container_elements_' + index +'"></div>').append(newFormFull);

    $('.add-element-link').fadeIn(function(){
        $('#container-element').prepend($newFormLi);
        $('#name_' + index).focus();
    });
    
    // Remove new declaration form
    $("#" + index).on('click', function (e) {
        e.preventDefault();
        removeElementForm($(this).attr('id'));
    });

    $("#count_elements").val(index + 1);
}

/**
 * Remove element form
 *
 * @param index
 */
function removeElementForm(index) {
    $("#container_elements_" + index).fadeOut(function(){
        $(this).remove();
    });
} 