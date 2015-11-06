<div id="b-assets-content">
	<?= View::make('boomcms::assets.thumbs', ['assets' => $assets]) ?>
</div>

<div id="b-assets-pagination" class="b-pagination">
    <a href="#" class="first" data-action="first">&laquo;</a>
    <a href="#" class="previous" data-action="previous">&lsaquo;</a>
    <input type="text" readonly="readonly" data-max-page="<?= $pages ?>" data-current-page="<?= $page ?>" />
    <a href="#" class="next" data-action="next">&rsaquo;</a>
    <a href="#" class="last" data-action="last">&raquo;</a>
</div>
