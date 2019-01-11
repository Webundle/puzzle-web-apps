$(function() {
    
    altair_form_adv.adv_selects();
});

altair_form_adv = {
    // advanced selects (selectizejs)
    adv_selects: function() {

        var stores = $("#hidden-list").val().split(",");
        var options = [];

        for(var i = 0; i < stores.length; i++){

            if(stores[i] != ""){
                options.push({id: i, title: stores[i]});
                options.push({id: 2, title: "Test"});
            }
        }
       

        $('#selec_adv_1').selectize({
            plugins: {
                'remove_button': {
                    label     : ''
                }
            },
            options: options ,
            maxItems: null,
            valueField: 'id',
            labelField: 'title',
            searchField: 'title',
            create: false,
            render: {
                option: function(data, escape) {
                    return  '<div class="option">' +
                            '<span class="title">' + escape(data.title) + '</span>' +
                            '</div>';
                },
                item: function(data, escape) {
                    return '<div class="item"><span class="title">' + escape(data.title) + '</span></div>';
                }
            },
            onDropdownOpen: function($dropdown) {
                $dropdown
                    .hide()
                    .velocity('slideDown', {
                        begin: function() {
                            $dropdown.css({'margin-top':'0'})
                        },
                        duration: 200,
                        easing: easing_swiftOut
                    })
            },
            onDropdownClose: function($dropdown) {
                $dropdown
                    .show()
                    .velocity('slideUp', {
                        complete: function() {
                            $dropdown.css({'margin-top':''})
                        },
                        duration: 200,
                        easing: easing_swiftOut
                    })
            }
        });
    }
};