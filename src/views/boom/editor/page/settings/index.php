<?php if ($auth->loggedIn('edit_page_navigation_basic', $page)): ?>
	<button id="boom-page-navigation" class="boom-button b-page-settings" data-b-page-setting="navigation">
		Navigation
	</button>
<?php endif ?>

<?php if ($auth->loggedIn('edit_page_urls', $page)): ?>
	<button id="boom-page-urls" class="boom-button b-page-settings" data-b-page-setting="urls">
		URLs
	</button>
<?php endif ?>

<?php if ($auth->loggedIn('edit_page_search_basic', $page)): ?>
	<button id="boom-page-search" class="boom-button b-page-settings" data-b-page-setting="search">
		Search
	</button>
<?php endif ?>

<button id="boom-page-tags" class="boom-button b-page-settings" data-b-page-setting="tags">
	Tags
</button>

<?php if ($auth->loggedIn('edit_page_children_basic', $page)): ?>
	<button id="boom-page-childsettings" class="boom-button b-page-settings" data-b-page-setting="childsettings">
		Child page settings
	</button>
<?php endif ?>

<?php if ($auth->loggedIn('edit_page_admin', $page)): ?>
	<button id="boom-page-adminsettings" class="boom-button b-page-settings" data-b-page-setting="adminsettings">
		Admin settings
	</button>
<?php endif ?>

<?php if ($auth->loggedIn('edit_page_template', $page)): ?>
	<button id="boom-page-template" class="boom-button">
		<?= Lang::get('Template') ?>
	</button>
<?php endif ?>

<button id="boom-page-embargo" class="boom-button">
	<?= Lang::get('Embargo') ?>
</button>

<?php if ($auth->loggedIn('edit_feature_image', $page)): ?>
	<button id="boom-page-featureimage" class="boom-button b-page-settings" data-b-page-setting="featureimage">
		<?= Lang::get('Feature image') ?>
	</button>
<?php endif ?>
