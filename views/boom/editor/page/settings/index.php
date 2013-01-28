<? if ($auth->logged_in('edit_page_navigation_basic', $page)): ?>
	<button id="boom-page-navigation" class="boom-button">
		Navigation
	</button>
<? endif; ?>

<? if ($auth->logged_in('edit_page_urls', $page)): ?>
	<button id="boom-page-urls" class="boom-button">
		URLs
	</button>
<? endif; ?>

<? if ($auth->logged_in('edit_page_search_basic', $page)): ?>
	<button id="boom-page-search" class="boom-button">
		Search
	</button>
<? endif; ?>

<button id="boom-page-tags" class="boom-button">
	Tags
</button>

<? if ($auth->logged_in('edit_page_children_basic', $page)): ?>
	<button id="boom-page-childsettings" class="boom-button">
		Child page settings
	</button>
<? endif; ?>

<? if ($auth->logged_in('edit_page_admin', $page)): ?>
	<button id="boom-page-adminsettings" class="boom-button">
		Admin settings
	</button>
<? endif; ?>

<? if ($auth->logged_in('edit_page_template', $page)): ?>
	<button id="boom-page-template" class="boom-button">
		<?= __('Template') ?>
	</button>
<? endif; ?>

<button id="boom-page-embargo" class="boom-button">
	<?= __('Embargo') ?>
</button>

<? if ($auth->logged_in('edit_feature_image', $page)): ?>
	<button id="boom-page-featureimage" class="boom-button">
		<?= __('Feature image') ?>
	</button>
<? endif; ?>
