<? if ($auth->loggedIn('edit_page_navigation_basic', $page)): ?>
	<button id="boom-page-navigation" class="boom-button b-page-settings" data-b-page-setting="navigation">
		Navigation
	</button>
<? endif; ?>

<? if ($auth->loggedIn('edit_page_urls', $page)): ?>
	<button id="boom-page-urls" class="boom-button b-page-settings" data-b-page-setting="urls">
		URLs
	</button>
<? endif; ?>

<? if ($auth->loggedIn('edit_page_search_basic', $page)): ?>
	<button id="boom-page-search" class="boom-button b-page-settings" data-b-page-setting="search">
		Search
	</button>
<? endif; ?>

<button id="boom-page-tags" class="boom-button b-page-settings" data-b-page-setting="tags">
	Tags
</button>

<? if ($auth->loggedIn('edit_page_children_basic', $page)): ?>
	<button id="boom-page-childsettings" class="boom-button b-page-settings" data-b-page-setting="childsettings">
		Child page settings
	</button>
<? endif; ?>

<? if ($auth->loggedIn('edit_page_admin', $page)): ?>
	<button id="boom-page-adminsettings" class="boom-button b-page-settings" data-b-page-setting="adminsettings">
		Admin settings
	</button>
<? endif; ?>

<? if ($auth->loggedIn('edit_page_template', $page)): ?>
	<button id="boom-page-template" class="boom-button">
		<?= __('Template') ?>
	</button>
<? endif; ?>

<button id="boom-page-embargo" class="boom-button">
	<?= __('Embargo') ?>
</button>

<? if ($auth->loggedIn('edit_feature_image', $page)): ?>
	<button id="boom-page-featureimage" class="boom-button b-page-settings" data-b-page-setting="featureimage">
		<?= __('Feature image') ?>
	</button>
<? endif; ?>
