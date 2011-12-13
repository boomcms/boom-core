<?php
	# Copyright 2011, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com	 mail@hoopassociates.co.uk
?>
<?
$ex = explode('/',@$_SERVER['REQUEST_URI']);
if (ctype_digit($ex[count($ex)-1])) {
	$this->pagen = $ex[count($ex)-1];
} else {
	$this->pagen = 1;
}
$total = O::f($this->page_model)->where("parent_rid = {$this->page->rid}")->find_all()->count();
$this->pages = ceil($total/10);
if ($this->pages <1) $this->pages = 1;
if ($this->pagen <1) $this->pagen = 1;
if ($this->pagen >$this->pages) $this->pagen = $this->pages;

switch ($this->page->child_ordering_policy_rid) {
	case 1:
		$orderby = 'sequence';
		$dir = 'asc';
		break;
	case 2:
		$orderby = 'title';
		$dir = 'asc';
		break;
	case 3:
		$orderby = 'visiblefrom_timestamp';
		$dir = 'desc';
		break;
}

$this->pagination_uri = $this->page->absolute_uri();
?>
<div class="wrapper">
	<?= new View('site/subtpl_siteheader');?>
	<div id="navigation">
		<?= new View('site/subtpl_logo');?>
		<?= new View('site/subtpl_leftnav');?>
		<?= O::f('chunk_linkset_v')->get_chunk(5, 'quicklinks', 'quicklinks');?>
	</div>
	<div id="main-content">
		<div class="headings">
			<h1 class="pageTitle"><?=$this->page->title;?></h1>
			<?= O::fa('chunk_text')->get_chunk($this->page->rid, 'standfirst', '<h2 class="standFirst">', '</h2>','ch,ins,br'); ?>
		</div>
		<?= O::fa('chunk_text')->get_chunk($this->page->rid, 'bodycopy', '<div id="content">', '</div>');?>
		<div class="library-pagination clearfix">
			<?=new View('site/subtpl_list_pagination')?>
		</div>
		<div class="list">
			<ul>
				<?foreach (O::f($this->page_model)->where("parent_rid = {$this->page->rid}")->orderby($orderby,$dir)->offset(($this->pagen-1) * 10)->limit(10)->find_all() as $page) {?>
					<li>
						<a href="<?=$page->absolute_uri()?>">
							<?$ca = O::fa('asset')->join('chunk_asset_v','chunk_asset_v.asset_rid','asset_v.rid')->join('chunk_asset','chunk_asset_v.id','chunk_asset.active_vid')->where("page_vid = $page->vid")->orderby('slotname','asc')->limit(1)->find();?>
								<?if ($ca->rid) {?>
									<img src="/_ajax/call/asset/get_asset/<?=$ca->rid?>/100/100/85/1" width="100" height="100" alt="<?=$ca->description?>">
								<?}else{
									$bodycopy = O::fa('chunk_text')->find_by_page_vid_and_slotname($page->vid,'bodycopy');
									if (preg_match('/hoopdb:\/\/asset\/([0-9]+)\//',$bodycopy->text,$m)) {
									$asset = O::fa('asset',$m[1])?>
									<img src="/_ajax/call/asset/get_asset/<?=$asset->rid?>/100/100/85/1" width="100" height="100" alt="<?=$asset->description?>">
								<?}?>
							<?}?>
						</a>
						<h3><a href="<?=$page->absolute_uri()?>"><?=$page->title?></a></h3>
						<p class="more">
							<?=O::fa('chunk_text')->find_by_page_vid_and_slotname($page->vid,'standfirst')->text?>
							<a href="<?=$page->absolute_uri()?>" title="<?=$page->title?>">Read more&nbsp;&raquo;</a>
						</p>
					</li>
				<?}?>
			</ul>
		</div>
		<div class="library-pagination clearfix">
			<?=new View('site/subtpl_list_pagination')?>
		</div>
	</div>
	<div id="aside">
		<?= O::f('chunk_feature_v')->get_chunk($this->page->rid, 'feature1', 'right');?>
	</div>
	<?= new View('site/subtpl_footer');?>
</div> 
