<?= view('boomcms::header', ['title' => trans('boomcms::asset.manager')]) ?>

<div id="b-assets-manager">
	<?= $menuButton() ?>
	<?= $menu() ?>

	<div id="b-topbar" class="b-asset-manager b-toolbar">
        <div id="tab-controls">
            <?= $button('th', 'view-albums', ['data-view' => '', 'data-active' => 'home']) ?>

            <?php if (Gate::allows('uploadAssets', Router::getActiveSite())): ?>
                <?= $button('upload', 'upload', ['data-view' => 'upload', 'data-active' => 'upload']) ?>
            <?php endif ?>

            <?= $button('search', 'search-assets', ['data-view' => 'search', 'data-active' => 'search']) ?>
        </div>
	</div>

    <main id="b-container">

    <div id="content">
    <table id="b-templates" class="b-table tablesorter">
        <thead>
            <tr>
                <th><?= trans('boomcms::template-manager.page-title') ?></th>
                <th><?= trans('boomcms::template-manager.url') ?></th>
                <th><?= trans('boomcms::template-manager.visible') ?></th>
                <th><?= trans('boomcms::template-manager.last-edited') ?></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach($assets as $asset) { ?>
                <tr>
                <td><?= $asset->getTitle() ?></td>
                <td><?= trans('boomcms::template-manager.url') ?></td>
                <td><?= trans('boomcms::template-manager.visible') ?></td>
                <td><?= trans('boomcms::template-manager.last-edited') ?></td>
            </tr>
                <?php } ?>
        </tbody>
    </table>
    </div>

  
    </main>

    <div>

    <pre>
        
    <?php 
            
            
            print_r($assets);
            
            
            
            ?>
      
      </pre>
      
     


    </div>

   
</div>


<script defer type="text/javascript" src="/vendor/boomcms/boom-core/js/asset-manager.js"></script>

<script type="text/javascript">
    window.onload = function() {
        new BoomCMS.AssetManager({
            albums: new BoomCMS.Collections.Albums(<?= Album::all() ?>)
        });
    };
</script>



<?= view('boomcms::footer') ?>
