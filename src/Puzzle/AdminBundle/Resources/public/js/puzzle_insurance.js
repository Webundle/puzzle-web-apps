// AutoComplete company
$("#autocomplete-company").kendoAutoComplete({
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
                url: $("#company-list-url").val()
            }
        }
    },
    select: function (e){ 
        var selectedOne =  this.dataItem(e.item.index());
        $("#company-id").val(selectedOne.id);
    },
    height: 200
}).data("kendoAutoComplete");

// AutoComplete movement
$("#autocomplete-movement").kendoAutoComplete({
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
                url: $("#movement-list-url").val()
            }
        }
    },
    select: function (e){ 
        var selectedOne =  this.dataItem(e.item.index());
        $("#movement-id").val(selectedOne.id);
    },
    height: 200
}).data("kendoAutoComplete");

// AutoComplete command
$("#autocomplete-command").kendoAutoComplete({
    minLength: 2,
    // filter : "contains",
    dataValueField : "id",
    dataTextField : "attestation",
    template: 
        '<div class="k-list-wrapper">'+
            '<span class="k-state-default k-list-wrapper-content">' +
                '<p>#: data.attestation #</p>' +
            '</span>' +
        '</div>',
    dataSource: {
        transport: {
            read: {
                method: 'POST',
                dataType: "json",
                url: $("#command-list-url").val()
            }
        }
    },
    select: function (e){ 
        var selectedOne =  this.dataItem(e.item.index());
        $("#command-id").val(selectedOne.id);
    },
    height: 200
}).data("kendoAutoComplete");

// AutoComplete customer
$("#autocomplete-customer").kendoAutoComplete({
    minLength: 2,
    // filter : "contains",
    dataValueField : "id",
    dataTextField : "fullName",
    template: 
        '<div class="k-list-wrapper">'+
            '<span class="k-state-default k-list-wrapper-content">' +
                '<p>#: data.fullName #</p>' +
            '</span>' +
        '</div>',
    dataSource: {
        transport: {
            read: {
                method: 'POST',
                dataType: "json",
                url: $("#customer-list-url").val()
            }
        }
    },
    select: function (e){ 
        var selectedOne =  this.dataItem(e.item.index());
        $("#customer-id").val(selectedOne.id);
    },
    height: 200
}).data("kendoAutoComplete");

// AutoComplete vehicle
$("#autocomplete-vehicle").kendoAutoComplete({
    minLength: 2,
    // filter : "contains",
    dataValueField : "id",
    dataTextField : "registrationNumber",
    template: 
        '<div class="k-list-wrapper">'+
            '<span class="k-state-default k-list-wrapper-content">' +
                '<p>#: data.registrationNumber #</p>' +
            '</span>' +
        '</div>',
    dataSource: {
        transport: {
            read: {
                method: 'POST',
                dataType: "json",
                url: $("#vehicle-list-url").val()
            }
        }
    },
    select: function (e){ 
        var selectedOne =  this.dataItem(e.item.index());
        $("#vehicle-id").val(selectedOne.id);
    },
    height: 200
}).data("kendoAutoComplete");

// AutoComplete vehicle brand
$("#autocomplete-vehicle-brand").kendoAutoComplete({
    minLength: 2,
    // filter : "contains",
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
                url: $("#vehicle-brand-list-url").val()
            }
        }
    },
    select: function (e){ 
        var selectedOne =  this.dataItem(e.item.index());
        $("#vehicle-brand-id").val(selectedOne.id);
    },
    height: 200
}).data("kendoAutoComplete");

// AutoComplete vehicle category
$("#autocomplete-vehicle-category").kendoAutoComplete({
    minLength: 2,
    // filter : "contains",
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
                url: $("#vehicle-category-list-url").val()
            }
        }
    },
    select: function (e){ 
        var selectedOne =  this.dataItem(e.item.index());
        $("#vehicle-category-id").val(selectedOne.id);
    },
    height: 200
}).data("kendoAutoComplete");