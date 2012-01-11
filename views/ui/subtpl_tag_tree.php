<?
if (!function_exists('recurse_subtpl_tag_tree')) {
	function recurse_subtpl_tag_tree($parent_tag, $tags, $selected) {?>
		<?foreach ($tags[$parent_tag->rid] as $tag) {?>
			<li>
				<a href="#tag/<?=$tag->rid?>"<?if (in_array($tag->rid,$selected)){?> class="ui-state-active"<?}?>>
					<?=$tag->name?>
				</a>
				<?if (isset($tags[$tag->rid])) {?>
					<ul class="ui-helper-hidden">
						<?=recurse_subtpl_tag_tree($tag, $tags, $selected)?>
					</ul>
				<?}?>
			</li>
		<?}?>
	<?}?>
<?}?>

<?
if (count($this->tags) and isset($this->tags[$this->basetag->rid])){
	foreach ($this->tags[$this->basetag->rid] as $tag) {?>

		<?if (!isset($this->toplevel_tag_is_header) or $this->toplevel_tag_is_header){?>
			<div class="sledge-tagmanager-box-header ui-helper-reset ui-widget-header ui-panel-header ui-corner-all">
				<?if (!in_array($tag->rid,$this->smart_tags)) {?>
					<a href="#" class="sledge-tagmanager-tags-edit ui-helper-right" rel="<?=$tag->rid?>">
						<span class="ui-icon ui-icon-wrench ui-helper-left"></span>
						Manage
					</a>
				<?}?>
				<h3 class="ui-helper-reset">
					<span class="ui-icon ui-icon-carat-1-e ui-helper-left"></span>
					<?=str_replace('Smart folders','Filters',$tag->name)?>
				</h3>
			</div>
		<?}?>
		<?if (isset($this->tags[$tag->rid])) {?>
			<div class="sledge-box">
				<ul class="ui-helper-clearfix sledge-tagmanager-tree sledge-tree-noborder">
					<?=recurse_subtpl_tag_tree($tag, $this->tags, $this->selected)?>
				</ul>
			</div>
		<?}?>
	<?}
}?>
