<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<?	
	// search results
	if(isset($this->sr)) {
		$find = $this->sr;
		$this->tag = O::fa('tag');
		$this->tag->name = 'Search Results';
	} else {
		$find = Tag::find($this->tag_rid, $this->basetag, $this->page, 30);
	}
?>
<div class="header">
	<div class="col3 actions">
		<!--
		<form id="assetsearchform" action="#" method="get" class="right" style="font-weight:normal;font-size:92%">
			<span class="loading hidden"> </span>
			<input type="hidden" value="Array" name="itemtypes"/>
			<input style="width:120px" id="asset_search" onfocus="this.value = this.value=='search..'?'':this.value" style="width: 150px;" value="search.." name="query"/>
		</form>
		-->
		<small>
			Sort by:
			<select name="sortby" id="change_sortby" style="padding:0px">
				<option value="audit_time"<?=($this->sortby=='audit_time')?' selected':'';?>>Date</option>
				<?if ($this->sortby == 'subject'){?>
					<option value="subject" selected>Subject</option>
				<?} else {?>
					<option value="title"<?=($this->sortby=='title')?' selected':'';?>>Name</option>
				<?}?>
			</select>
			&nbsp;
			View as:
			<select name="view" id="change_view" style="padding:0px">
				<option value="list">List</option>
				<option value="thumb">Thumbnails</option>
			</select>
		</small>
	</div>
	<div class="col3"><?=$this->tag->name?> &nbsp;(<?=$this->nr?> items)</div>
</div>

<?
	function recurse($tag_contents, $c, $last=false) {
		$ki = Kohana::Instance();
		if (!isset($tag_contents->item_tablename) or !($tag_contents->item_tablename)) $tag_contents->item_tablename = preg_replace('/_v$/','',$tag_contents->table);
		$ki->type = $tag_contents->item_tablename;			
		$ki->recursing = array();
		$ki->recursing['tag_contents'] = O::fa($ki->type, $tag_contents->rid);
		$ki->recursing['c'] = $c;
		$ki->recursing['last'] = $last;
		/*if ((int)$ki->recursing['tag_contents']->from_rid > 0) {
			$ki->recursing['tag_contents'] = O::fa($ki->type)->where($ki->type.".id=".$ki->recursing['tag_contents']->to_rid." and deleted is not true")->find(false, false, true);
		}*/
		$ki->recursing["rubbishrow"] = false; //($ki->recursing["tag_contents"]->rubbish=='t' and $ki->tag->name!='Rubbish') ? true : false;
		
		if (!$ki->recursing['tag_contents']->id || $ki->recursing['tag_contents']->ref_status_rid != '2') {
			return false;
		}
		?>
		<li<?if ($last and $ki->pagination->total_pages > 1) {echo ' class="last"';}?>>
			<div class="row message"> <? /*<?=$tag_contents->tablename;?>">*/ ?>
			<?
				$rowtype = $ki->type;
				$ki->recursing["rowtype"] = $rowtype;

				# surely not..
				#$rowtype = preg_replace("/cms_person/", "person", $rowtype);
				echo new View('site/subtpl_tag_appliedto_'.$ki->rowview.'_row_' . $rowtype); 
			?>

			</div>
		</li>
		<?
	}
?>
<?
	if (sizeof($find)) {
		?><ul class="assets tree" style="margin-top:4px;margin-bottom:10px;"><?
		$c = 1;
		foreach($find as $tag_contents) {
			$this->recursion_count = 1;
			recurse($tag_contents, $this->recursion_count, $c == sizeof($find));
			$c++;
		}
		?></ul><?
	} 
	if ($this->pagination->total_pages > 1) {
		echo $this->pagination->create_links();
	}
?>
