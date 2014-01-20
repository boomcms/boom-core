$.widget('boom.tagAutocompleter', {
	
	exclude_tags : [],

	onSelect : null,

	type : null,

	_create : function() {
		var self = this;

		this.type = this.options.type;
		this.onSelect = this.options.onSelect;

		this.element.autocomplete({
			delay: 200, // Time to wait after keypress before making the AJAX call.
			source : this._autocompleteSource(),
			select : this._autocompleteSelect()
		})
		.on('keypress', function(e) {
			// Add a tag when the enter key is pressed.
			// This allows us to add a tag which doesn't already exist.
			if (e.which == 13) {
				self._tagSelected(this.element.val());
			}
		});
	},

	_autocompleteSource : function(request, response) {
		$.ajax({
			url: '/cms/autocomplete/tags',
			dataType: 'json',
			data: {
				text : this.element.val(),
				type : (this.type == 'asset')? 1 : 2,
				tags : this.exclude_tags
			}
		})
		.done(function(data) {
			var suggestions = [], name;

			for (name in data) {
				suggestions.push({
					label : name,
					value : data[name]
				});
			}

			response(suggestions);
		});
	},

	_autocompleteSelect : function(event, ui) {
		event.preventDefault();
		this.element.val( '' );
		this._tagSelected(ui.item);
	},

	_tagSelected : function(name) {
		if (typeof this.onSelect == 'function') {
			this.onSelect(name);
		}
	}
});