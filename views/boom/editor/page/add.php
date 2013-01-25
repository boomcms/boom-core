<form id="b-page-add-form">
	
	<label for="parent_id">
		<?=__('Parent page')?>
	</label>

	<input type="hidden" name="parent_id" value="<?=$page->id?>">
	<div class="boom-tree">
		<ul>
			<li><a id="page_5" href="/" rel="5">Home</a></li>
		</ul>
	</div>

	
<label for="template_id"><?=__('Template')?>
<?= Form::select('template_id', $templates, $default_template, array('style' => 'width: 24em')); ?>
</label>
</form>