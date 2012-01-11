<?function recurse_subtpl_tag_tree($parent_tag, $tags, $selected) {?>
	<?foreach ($tags[$parent_tag->rid] as $tag) {?>
		<li>
			<a href="#tag/<?=$tag->rid?>" rel="<?=$tag->rid?>"<?if (in_array($tag->rid,$selected)){?> class="ui-state-active"<?}?>>
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

<?if (count($this->tags) and isset($this->tags[$this->basetag->rid])){?>
	<ul class="ui-helper-clearfix sledge-tree">
		<?foreach ($this->tags[$this->basetag->rid] as $tag) {?>

			<li>
				<a href="#tag/<?=$tag->rid?>" rel="<?=$tag->rid?>"<?if (in_array($tag->rid,$this->selected)){?> class="ui-state-active"<?}?>>
					<?=$tag->name?>
				</a>

				<?if (isset($this->tags[$tag->rid])) {?>
					<ul class="ui-helper-hidden">
						<? recurse_subtpl_tag_tree($tag, $this->tags, $this->selected); ?>
					</ul>
				<?}?>
			</li>
		<?}?>
	</ul>
<?} else {?>

	<p>
		<em>(No tags)</em>
	</p>
<?}?>
