$(document).ready(function() {
    $('.b-approvals-publish').on('click', function(e) {
        e.preventDefault();

        var $this = $(this), page = boom_approvals_get_page($this);
        page.publish();

        BoomCMS.Notification('All changes to this page are now published').show();
        boom_approvals_remove_row($this);
    });

    $('.b-approvals-reject').on('click', function(e) {
        e.preventDefault();

        var $this = $(this), page = boom_approvals_get_page($this);

        page
            .revertToPublished()
            .done(function() {
                BoomCMS.Notification('This page has been reverted to it\'s most recent published version.').show();
                boom_approvals_remove_row($this);
            });
    });

    function boom_approvals_get_page($element) {
        var page_id = $element.parents('tr').attr('data-page-id');

        return new BoomCMS.Page({id: page_id});
    }

    function boom_approvals_remove_row($element) {
        $element.parents('tr').remove();
    }
});