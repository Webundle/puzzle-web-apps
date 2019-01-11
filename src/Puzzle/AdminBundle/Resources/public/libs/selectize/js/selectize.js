var formatName = function(item) {
	return $.trim((item.first_name || '') + ' ' + (item.last_name || ''));
};

$('.autocomplete').selectize({
	valueField: 'id',
    labelField: 'value',
    searchField: ['value'],
    delimiter: ",",
    options: [],
	render: {
        item: function(item, escape) {
            return '<div>' +
                (item.name ? '<span class="value">' + escape(item.name) + '</span>' : '') +
            '</div>';
        },
        option: function(item, escape) {
            var label = item.name;
             return '<div>' + '<span class="text-primary">' + escape(label) + '</span></div>';
        }
    },
    
	load: function(query, callback) {
		if (!query.length) return callback();

        var url = $("#movement-list-url").val();

		$.post(url,{name: query}, function(data) {
			callback(data);
		});
	}
});