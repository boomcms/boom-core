<?= Boom::include_css() ?>
<?= $body_tag ?>
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

<iframe frameBorder="0" style="position: fixed; left: 0; top: 0; bottom: 0; width: 60px; height: 100%; overflow: hidden; z-index: 10000; background: transparent; <? if (Editor::instance()->state() !== Editor::EDIT): ?>border: none; width: 100px; right: 0; <? endif; ?>" id='b-page-topbar' scrolling="no" src='/cms/editor/toolbar/<?= $page_id ?>'></iframe>