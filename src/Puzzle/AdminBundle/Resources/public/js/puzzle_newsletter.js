// AutoComplete
$("#autocomplete-receivers").kendoAutoComplete({
    minLength: 2,
    filter : "contains",
    separator: ", ",
    dataValueField : "id",
    dataTextField : "email",
    template: 
        '<div class="k-list-wrapper">'+
            '<span class="k-state-default k-list-wrapper-content">' +
                '<p>#: data.email #</p>' +
            '</span>' +
        '</div>',
    dataSource: {
        transport: {
            read: {
                method: 'POST',
                dataType: "json",
                url: $("#receivers-list-url").val()
            }
        }
    },
    select: function (e){ 
        var selectedOne =  this.dataItem(e.item.index());
        $("#receivers-id").val(selectedOne.id);
    },
    height: 200
}).data("kendoAutoComplete");

$("body").on('mouseenter', '.toggleable', togglize);
$("body").on('mouseleave', '.toggleable', untogglize);
