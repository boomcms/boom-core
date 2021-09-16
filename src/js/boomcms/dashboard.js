(function ($) {
    'use strict';

    $(document).on('click', '#btn-recently-added', function () {
        $(this).addClass('active');
        $('#btn-pending-approval').removeClass('active');

        $('#recently-added-pages').removeClass('hidden');
        $('#pending-approval-pages').addClass('hidden');
    });

    $(document).on('click', '#btn-pending-approval', function () {
        $(this).addClass('active');
        $('#btn-recently-added').removeClass('active');

        $('#recently-added-pages').addClass('hidden');
        $('#pending-approval-pages').removeClass('hidden');
    });

})(jQuery);
