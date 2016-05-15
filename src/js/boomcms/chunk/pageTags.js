/**
 * Enables opening the page tag editor by clicking on a list of tags in the page.
 */
$.widget('ui.chunkPageTags', {

	_create: function() {
		var chunkPageTags = this;

		this.element
			.addClass(BoomCMS.editableClass)
			.on('click', function(e) {
				e.preventDefault();
				e.stopPropagation();

				new boomPageTagEditor(chunkPageTags.options.currentPage)
					.done(function() {

						// The tag list could be a filtered list or have other logic around it's generation
						// So to update the list do an AJAX call for the window location and list the element contents.
						chunkPageTags.element.load(top.location + '  .b-chunk-pagetags > *');
					});
			});
	}
});