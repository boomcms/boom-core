$.widget('boom.assetTitleFilter', {
	options : {
		delay: 400,
		minLength: 3
	},

	_create: function() {
		var element = this.element;

		this.options.source = function(request, response) {
			$.ajax({
				url: '/boomcms/autocomplete/assets',
				dataType: 'json',
				data: {
					text : element.val()
				}
			})
			.done(function(data) {
				response(data);
			});
		};

		this.element.autocomplete(this.options);
	}
});