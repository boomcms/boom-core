<?
$types = 'asset';

$this->sortby = 'audit_time';
$this->order = 'desc';
$this->types = array();
foreach (O::fa('ref_relationship_tables')->find_all() as $type) {
	if ($type->name == $types) $this->types[] = $type;
}

$this->basetag = Tag::find_or_create_tag(1,'Assets');
$this->tag = $this->basetag;
$this->basetag_rid = $this->basetag->rid;
$this->tag_rid = $this->basetag_rid;

$find = Tag::find($this->tag_rid, $this->basetag_rid, 1, 30);

?>
<div class="sledge-tagmanager-main ui-helper-right">
	<div class="sledge-tagmanager-body ui-helper-clearfix">
		<div class="sledge-tagmanager-rightpane">
			<div class="content">
				<?
				$total = 0;
				foreach ($find as $item) {

					if ($this->basetag->name == 'Assets') {
						$total += $item->filesize;
					}

					if (isset($item->item_tablename) && @$item->item_tablename) {
						$table = $item->item_tablename;
					} else if (isset($item->tablename) && @$item->tablename) {
						$table = $item->tablename;
					} else {
						$table = preg_replace('/_v_model$/','',strtolower(get_class($item)));
					}

					echo new View('cms/ui/subtpl_tag_appliedto_thumb_row_' . $table, array('item'=>$item));
				}?>
			</div>
		</div>

	</div>
</div>
<div class="sledge-tagmanager-sidebar ui-helper-left">

	<?=Tag::get_tag_tree('cms',$this->basetag_rid);?>
</div>
<script>
$( function() {
	$.sledge.tagmanager.assets.init({
		items: {
			tag: $.sledge.tagmanager.items.tag,
			asset: $.sledge.tagmanager.items.asset
		},
		options: {
			sortby: 'NULL',
			order: 'NULL',
			basetagRid: 2, 
			defaultTagRid: 2219,
			edition: 'cms',
			type: 'asset_manager',
			selected: [''], 
			types: ['asset'],
			excludeSmartTags: 0,
			template: 'thumb'
		}
	});
});
</script>