/**
 * Enables opening the page visibility editor by clicking on an element in the page.
 */
$.widget('ui.chunkPageVisibility', {
	bind: function() {
		var chunkPageVisibility = this;

		this.element
			.addClass('b-editable')
			.on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				new boomPageVisibilityEditor(chunkPageVisibility.options.currentPage)
					.done(function() {
						$.get(top.location.href).done(function(response) {
							var newElement = $(response).find('.b-chunk-pagevisibility');

							chunkPageVisibility.element.replaceWith(newElement);
							chunkPageVisibility.element = newElement;
							chunkPageVisibility.bind();
						});
					});
			});
	},

	_create: function() {
		this.bind();
	}
});