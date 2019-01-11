// AutoComplete
$("#autocomplete-parent").kendoAutoComplete({
    minLength: 2,
    filter : "contains",
    dataValueField : "id",
    dataTextField : "name",
    template: 
        '<div class="k-list-wrapper">'+
            '<span class="k-state-default k-list-wrapper-content">' +
                '<p>#: data.name #</p>' +
            '</span>' +
        '</div>',
    dataSource: {
        transport: {
            read: {
                method: 'POST',
                dataType: "json",
                url: $("#parent-list-url").val()
            }
        }
    },
    select: function (e){ 
        var selectedOne =  this.dataItem(e.item.index());
        $("#parent-id").val(selectedOne.id);
    },
    height: 200
}).data("kendoAutoComplete");

// AutoComplete
$("#autocomplete-category").kendoAutoComplete({
    minLength: 2,
    filter : "contains",
    dataValueField : "id",
    dataTextField : "name",
    template: 
        '<div class="k-list-wrapper">'+
            '<span class="k-state-default k-list-wrapper-content">' +
                '<p>#: data.name #</p>' +
            '</span>' +
            '<span class="k-state-default k-list-wrapper-content">' +
                '<p>#: data.parent #</p>' +
            '</span>' +
        '</div>',
    dataSource: {
        transport: {
            read: {
                method: 'POST',
                dataType: "json",
                url: $("#category-list-url").val()
            }
        }
    },
    select: function (e){ 
        var selectedOne =  this.dataItem(e.item.index());
        $("#category-id").val(selectedOne.id);
    },
    height: 200
}).data("kendoAutoComplete");


$("body").on('mouseenter', '.toggleable', togglize);
$("body").on('mouseleave', '.toggleable', untogglize);
