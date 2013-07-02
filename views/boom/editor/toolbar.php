<?= View::factory('boom/header', array('title' => $page->version()->title)) ?>

<div id="boom-topbar" class="ui-helper-clearfix ui-tabs ui-widget ui-widget-content ui-corner-all">

	<?= Menu::factory('boom')->sort('priority') ?>

	<div id="boom-topbar-useractions">
		<button id="boom-page-menu" class="ui-button boom-button" data-icon="ui-icon-boom-menu">
				<?=__('Menu')?>
		</button>
		<span id="boom-page-user-menu">
			<button id="boom-page-user" class="ui-button boom-button" data-icon="ui-icon-boom-person">
				<?=__('Profile')?>
			</button>
		</span>
	</div>

	<? if (Kohana::$environment !== Kohana::PRODUCTION): ?>
		<? $class = new ReflectionClass('Kohana');
		$constants = $class->getConstants();
		$constants = array_flip($constants);
		$environment = $constants[Kohana::$environment];
		$branchname = '';

		if ( Kohana::$environment == Kohana::DEVELOPMENT ):
			$dir = DOCROOT;
			exec( "cd '$dir'; git branch", $lines );
			foreach ( $lines as $line ) {
			    if ( strpos( $line, '*' ) === 0 ) {
			        $branchname = '<br>' . ltrim( $line, '* ' );
			        break;
			    }
			}
		endif;

		?>
		<div id="b-environment">
			<p><?= $environment ?> site <?= $branchname ?></p>
		</div>
	<? endif; ?>

	<? if ($page->was_created_by($person) OR $auth->logged_in('edit_page_content', $page)): ?>
		<div id="b-page-actions" class="b-page-container">
			<span id="boom-page-save-menu">
				<button id="b-page-save" class="ui-button boom-button" disabled="disabled" title="You have no unsaved changes" data-icon="ui-icon-boom-accept">
					<?=__('Accept')?>
				</button>
			</span>
			<button id="b-page-cancel" class="ui-button boom-button" disabled="disabled" data-icon="ui-icon-boom-cancel">
					<?=__('Cancel')?>
			</button>
		</div>

		<div class="b-page-container">
			<button id="b-page-version-status" class="ui-button boom-button">
				<?= __($page->status()) ?>
			</button>

			<button id="boom-page-preview" class="boom-button b-button-preview" data-icon="ui-icon-boom-preview" data-preview="preview">
				<?=__('Preview')?>
			</button>

			<span id="boom-page-template-menu">
				<button id="boom-page-template-settings" class="ui-button boom-button" data-icon="ui-icon-boom-options">
					<?= __('Version settings') ?>
				</button>
			</span>
		</div>
	<? endif; ?>

	<? if ($auth->logged_in('add_page', $page)): ?>
		<div class="b-page-container">
			<button id="b-page-addpage" class="ui-button boom-button" data-icon="ui-icon-boom-add">
				<?=__('Add')?>
			</button>
		</div>
	<? endif; ?>

	<? if ($auth->logged_in('edit_page', $page)): ?>
		<div class="b-page-container">
			<button id="boom-page-visibility" class="ui-button boom-button" data-icon="ui-icon-boom-<? echo ($page->is_visible())? 'visible' : 'invisible' ?>">
				<?= __('Visibility') ?>
			</button>
			<span id="boom-page-settings-menu">
				<button id="boom-page-settings" class="ui-button boom-button" data-icon="ui-icon-boom-settings">
					<?= __('Settings') ?>
				</button>
			</span>
			<? if (($page->was_created_by($person) OR $auth->logged_in('delete_page', $page)) AND ! $page->mptt->is_root()): ?>
				<button class="ui-button boom-button" id="b-page-delete" data-icon="ui-icon-boom-delete">
					<?= __('Delete') ?>
				</button>
			<? endif; ?>
		</div>

		<? if ($readability): ?>
			<button id="b-page-readability" class="ui-button boom-button">
				<?= $readability ?>
			</button>
		<? endif ?>
	<? endif; ?>

	<? if ($auth->logged_in('edit_page', $page)): ?>
		<div class="b-page-container">
			<button id="boom-page-editlive" class="ui-button boom-button" data-icon="ui-icon-boom-edit-live">
				<?=__('Edit live')?>
			</button>

			<button id="boom-page-viewlive" class="boom-button b-button-preview" data-icon="ui-icon-boom-view-live" data-preview="disabled">
				<?=__('View live')?>
			</button>
		</div>
	<? endif; ?>

	<div id="boom-topbar-pagesettings" class="ui-helper-clearfix">
		<div class="ui-helper-center">
			<?= View::factory('boom/editor/page/settings/index');?>
		</div>
	</div>
</div>

<?= View::factory('boom/editor/footer', array('register_page' => TRUE)) ?>