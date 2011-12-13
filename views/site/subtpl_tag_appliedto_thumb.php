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
				<option value="title"<?=($this->sortby=='title')?' selected':'';?>>Name</option>
			</select>
			&nbsp;
			View as:
			<select name="view" id="change_view" style="padding:0px">
				<option value="list">List</option>
				<option value="thumb" selected="selected">Thumbnails</option>
			</select>
		</small>
	</div>
	<div class="col3" title="<?=$this->tag->name;?>"><?=$this->tag->name?> &nbsp;(<?=$this->nr?> items)</div>
</div>

<div style="width:100%;" class="thumbs">

<?
	function recurse($tag_contents, $c, $last=false) {
		if (!isset($tag_contents->item_tablename) or !($tag_contents->item_tablename)) $tag_contents->item_tablename = preg_replace('/_v$/','',$tag_contents->table);
		$ki = Kohana::Instance();
		$ki->type = $tag_contents->item_tablename;
		$ki->recursing = array();
		$ki->recursing['tag_contents'] = O::fa($tag_contents->item_tablename, $tag_contents->rid);
		$ki->recursing['c'] = $c;
		$ki->recursing['last'] = $last;
		/*if ((int)$ki->recursing['tag_contents']->from_rid > 0) {
			$ki->recursing['tag_contents'] = O::fa($ki->type)->where($ki->type.".id=".$ki->recursing['tag_contents']->to_rid)->find(false, false, true);
		}*/
		$ki->recursing["rubbishrow"] = false; //($ki->recursing["tag_contents"]->rubbish=='t' and $ki->tag->name!='Rubbish') ? true : false;
                if (!$ki->recursing['tag_contents']->id || $ki->recursing['tag_contents']->ref_status_rid != '2') {
                        return false;
                }

		$rowtype = $tag_contents->item_tablename;	
		$ki->recursing["rowtype"] = $rowtype;
		echo new View('site/subtpl_tag_appliedto_'.$ki->rowview.'_row_' . $rowtype); 
	}
?>
<?
	if (sizeof($find)) {
		$c = 1;
		foreach($find as $tag_contents) {
			$this->recursion_count = 1;
			recurse($tag_contents, $this->recursion_count, $c == sizeof($find));
			$c++;
		}
	} 
?>
</div>
<div class="clear"></div><br/>
<?
	if ($this->pagination->total_pages > 1) {
		echo $this->pagination->create_links();
	}
?>
