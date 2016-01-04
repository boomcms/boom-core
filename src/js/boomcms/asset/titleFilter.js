$.widget('boom.assetTitleFilter', {
	options : {
		delay : 200,
		minLength : 1
	},

	_create : function() {
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
		this.element
			.on('focus', function() {
				if (element.val() === element.attr('placeholder')) {
					element.val('');
				}
			})
			.on('blur', function() {
				if (element.val() === '') {
					element.val(element.attr('placeholder'));
				}
			});
	}
});