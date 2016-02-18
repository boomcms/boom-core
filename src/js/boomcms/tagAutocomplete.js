$.widget('boom.tagAutocomplete',  {
	url : '',
	ignoreTags : [],

	bind: function() {
		var self = this;

		this.element
			.on('keypress', function(e) {
				// Add a tag when the enter key is pressed.
				// This allows us to add a tag which doesn't already exist.
				if (e.which == 13 && self.element.val()) {
					e.preventDefault();
					
					var tags, i;

					// If the text entered contains commas then it will be treated as a list of tags with each one 'completed'
					tags = self.element.val().split(',');

					for (i = 0; i < tags.length; i++) {
						self.tagSelected(tags[i]);
					}
					
					self.element.val('');
					self.element.autocomplete('close');
				}
			});
	},

	_create: function() {
		var self = this;

		this.group = this.options.group;

		this.element.parents().find('.b-dialog').css('overflow', 'visible');

		this.element.autocomplete({
			delay: 200,
			source: function(request, response) {
				self._autocompleteSource(request, response);
			},
			focus: function(event, ui) {
				event.preventDefault();

				self.element.val(ui.item.label);
			},
			select: function(event, ui) {
				event.preventDefault();

				self.element.val('');
				self.tagSelected(ui.item);
			}
		});

		this.bind();
	},

	_autocompleteSource: function(request, response) {
		$.ajax({
			url: this.url,
			dataType: 'json',
			data: {
				group : this.group,
				text : this.element.val(),
				ignore : this.ignoreTags
			}
		})
		.done(function(data) {
			response(data);
		});
	},

	setIgnoreTags: function(tags) {
		this.ignoreTags = tags;
	},

	tagSelected: function(tag) {
		this._trigger('complete', null, {tag : tag});
	}
});