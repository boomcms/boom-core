$.widget('boom.assetTitleFilter', {
	options : {
		delay : 200,
		minLength : 0
	},

	_create : function() {
		var element = this.element;

		this.options.source = function(request, response) {
			$.ajax({
				url: '/cms/autocomplete/assets',
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
				var $this = $(this);

				if ($this.val() === $this.attr('placeholder')) {
					$this.val('');
				}
			})
			.on('blur', function() {
				var $this = $(this);

				if ($this.val() === '') {
					$this.val($this.attr('placeholder'));
				}
			});
	}
});