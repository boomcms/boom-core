<?

foreach ($tags as $tag):
	if ($tag->mptt->is_root()):
		?>
			<div class="sledge-tagmanager-box-header ui-helper-reset ui-widget-header ui-panel-header ui-corner-all">
				<?
					if (!$tag->is_smart()):
						?>
							<a href="#" class="sledge-tagmanager-tags-edit ui-helper-right" rel="<?=$tag->rid?>">
								<span class="ui-icon ui-icon-wrench ui-helper-left"></span>
								Manage
							</a>
						<?
					endif;
				?>
				<h3 class="ui-helper-reset">
					<span class="ui-icon ui-icon-carat-1-e ui-helper-left"></span>
					<?= str_replace('Smart folders','Filters', $tag->name)?>
				</h3>
			</div>
		<?
	endif;
	?>

	<div class="sledge-box">
		<ul class="ui-helper-clearfix sledge-tagmanager-tree sledge-tree-noborder">
			Child tags.
		</ul>
	</div>
<?
endforeach;
?>