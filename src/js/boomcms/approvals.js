$(document).ready(function() {
	$('.b-approvals-publish').on('click', function(e) {
		e.preventDefault();

		var $this = $(this), page = boom_approvals_get_page($this);
		page.publish();

		new boomNotification('All changes to this page are now published');
		boom_approvals_remove_row($this);
	});

	$('.b-approvals-reject').on('click', function(e) {
		e.preventDefault();

		var $this = $(this), page = boom_approvals_get_page($this);

		page
			.revertToPublished()
			.done(function() {
				new boomNotification("This page has been reverted to it's most recent published version.");
				boom_approvals_remove_row($this);
			});
	});

	function boom_approvals_get_page($element) {
		var page_id = $element.parents('tr').attr('data-page-id');

		return new boomPage(page_id);
	}

	function boom_approvals_remove_row($element) {
		$element.parents('tr').remove();
	}
});