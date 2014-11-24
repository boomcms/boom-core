$.widget('boom.tagAutocompleter', {

	onSelect : null,

	selectedTags : [],

	type : null,

	_create : function() {
		var self = this;

		this.element.parents().find('.b-dialog').css('overflow', 'visible');

		this.type = this.options.type == 'asset' || this.options.type == 1? 1 : 2;
		this.onSelect = this.options.onSelect;

		this.element.autocomplete({
			delay: 200, // Time to wait after keypress before making the AJAX call.
			source : function(request, response) {
				self._autocompleteSource(request, response);
			},
			focus : function(event, ui) {
				event.preventDefault();

				self.element.val(ui.item.label);
			},
			select : function(event, ui) {
				event.preventDefault();

				self.element.val('');
				self._tagSelected(ui.item.label, ui.item.value);
			}
		})
		.on('keypress', function(e) {
			// Add a tag when the enter key is pressed.
			// This allows us to add a tag which doesn't already exist.
			if (e.which == 13 && self.element.val()) {
				self._tagSelected(self.element.val(), -1);
				self.element.val('');
				self.element.autocomplete('close');
			}
		})
		.on('focus', function() {
			if (self.element.val() === self.element.attr('placeholder')) {
				self.element.val('');
			}
		})
		.on('blur', function() {
			if (self.element.val() === '') {
				self.element.val(self.element.attr('placeholder'));
			}
		});
	},

	_autocompleteSource : function(request, response) {
		$.ajax({
			url: '/cms/autocomplete/tags',
			dataType: 'json',
			data: {
				text : this.element.val(),
				type : this.type,
				tags : this.selectedTags
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

	setSelectedTags : function(tags) {
		this.selectedTags = tags;
	},

	_tagSelected : function(name, id) {
		this._trigger('complete', null, {name : name, id : id});
	}
});