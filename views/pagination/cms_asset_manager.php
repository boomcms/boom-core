<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
?>
<?php
if (!isset($this->tag) or $this->tag->rid == '') {
	// set the base tag
	$this->tag = O::fa('tag')->find_by_name('All assets');
}

$sortby = str_replace(' ', '-', $this->sortby);

if (!isset($this->order)) $this->order = 'name';

/**
 * 
 * @preview  « Previous  1 2 … 5 6 7 8 9 10 11 12 13 14 … 25 26  Next »
 */
?>
<div class="pagination">
	<?php if ($previous_page): ?>
		<? if (!isset($_GET['query'])){?>
			<a rel="ajax" id="page_prev_<?=$previous_page?>" href="#tag/<?=$this->tag->rid?>/<?=$sortby?>/<?=$this->order?>/<?=$previous_page?>">&laquo;&nbsp;prev</a>
		<?} else {?>
			<a rel="ajax" id="page_prev_<?=$previous_page?>" href="#search/<?=$_GET['query'];?>/<?=$sortby?>/<?=$this->order?>/<?=$this->page?>">&laquo;&nbsp;prev</a>
		<?}?>
		<!--<a href="<?php echo str_replace('{page}', $previous_page, $url) ?>">&laquo;&nbsp;<?php echo Kohana::lang('pagination.previous') ?></a>-->
	<?php else: ?>
	<!--&laquo;&nbsp;<?php echo Kohana::lang('pagination.previous') ?>-->
	<?php endif ?>


	<?php if ($total_pages < 13): /* « Previous  1 2 3 4 5 6 7 8 9 10 11 12  Next » */ ?>
		<?php for ($i = 1; $i <= $total_pages; $i++): ?>
			<?php if ($i == $current_page): ?>
				<strong style="display: inline;"><?php echo $i ?></strong>
			<?php else: ?>
				<? if (!isset($_GET['query'])){?>
					<a rel="ajax" id="page_<?=$i?>" href="#tag/<?=$this->tag->rid?>/<?=$sortby?>/<?=$this->order?>/<?=$i?>"><?=$i?></a>
				<?} else {?>
					<a rel="ajax" id="page_<?=$i?>" href="#search/<?=$_GET['query']?>/<?=$sortby?>/<?=$this->order?>/<?=$i?>"><?=$i?></a>
				<?}?>
				<!--<a href="<?php echo str_replace('{page}', $i, $url) ?>"><?php echo $i ?></a>-->
			<?php endif ?>
		<?php endfor ?>

	<?php elseif ($current_page < 9): /* « Previous  1 2 3 4 5 6 7 8 9 10 … 25 26  Next » */ ?>

		<?php for ($i = 1; $i <= 10; $i++): ?>
			<?php if ($i == $current_page): ?>
				<strong style="display: inline;"><?php echo $i ?></strong>
			<?php else: ?>
				<? if (!isset($_GET['query'])){?>
					<a rel="ajax" id="page_<?=$i?>" href="#tag/<?=$this->tag->rid?>/<?=$sortby?>/<?=$this->order?>/<?=$i?>"><?=$i?></a>
				<?} else {?>
					<a rel="ajax" id="page_<?=$i?>" href="#search/<?=$_GET['query']?>/<?=$sortby?>/<?=$this->order?>/<?=$i?>"><?=$i?></a>
				<?}?>
			<?php endif ?>
		<?php endfor ?>

		&hellip;
		<? if (!isset($_GET['query'])){?>
			<a rel="ajax" id="page_<?=($total_pages-1)?>" href="#tag/<?=$this->tag->rid?>/<?=$sortby?>/<?=$this->order?>/<?=($total_pages-1)?>"><?=($total_pages-1)?></a>
			<a rel="ajax" id="page_<?=($total_pages-1)?>" href="#tag/<?=$this->tag->rid?>/<?=$sortby?>/<?=$this->order?>/<?=($total_pages)?>"><?=($total_pages)?></a>
			<!--<a href="<?php echo str_replace('{page}', $total_pages - 1, $url) ?>"><?php echo $total_pages - 1 ?> </a>-->
			<!--<a href="<?php echo str_replace('{page}', $total_pages, $url) ?>"><?php echo $total_pages ?></a>-->
		<?} else {?>
			<a rel="ajax" id="page_<?=($total_pages-1)?>" href="#search/<?=$_GET['query']?>/<?=$sortby?>/<?=$this->order?>/<?=($total_pages-1)?>"><?=($total_pages-1)?></a>
			<a rel="ajax" id="page_<?=($total_pages-1)?> href="#search/<?=$_GET['query']?>/<?=$sortby?>/<?=$this->order?>/<?=($total_pages)?>"><?=($total_pages)?></a>
		<?}?>
	<?php elseif ($current_page > $total_pages - 8): /* « Previous  1 2 … 17 18 19 20 21 22 23 24 25 26  Next » */ ?>

		<? if (!isset($_GET['query'])){?>
			<a rel="ajax" id="page_<?=$this->tag->rid?>" href="#tag/<?=$this->tag->rid?>/<?=$sortby?>/<?=$this->order?>/1">1</a>
			<a rel="ajax" id="page_<?=$this->tag->rid?>" href="#tag/<?=$this->tag->rid?>/<?=$sortby?>/<?=$this->order?>/2">2</a>
		<?} else {?>
			<a rel="ajax" id="page_1" href="#search/<?=$_GET['query']?>/<?=$sortby?>/<?=$this->order?>/1">1</a>
			<a rel="ajax" id="page_2" href="#search/<?=$_GET['query']?>/<?=$sortby?>/<?=$this->order?>/2">2</a>
		<?}?>		
		&hellip;

		<?php for ($i = $total_pages - 9; $i <= $total_pages; $i++): ?>
			<?php if ($i == $current_page): ?>
				<strong style="display: inline;"><?php echo $i ?></strong>
			<?php else: ?>
				<? if (!isset($_GET['query'])){?>
					<a rel="ajax" id="page_<?=$i?>" href="#tag/<?=$this->tag->rid?>/<?=$sortby?>/<?=$this->order?>/<?=$i?>"><?=$i?></a>
				<?} else {?>
					<a rel="ajax" id="page_<?=$i?>" href="#search/<?=$_GET['query']?>/<?=$sortby?>/<?=$this->order?>/<?=$i?>"><?=$i?></a>
				<?}?>	
			<?php endif ?>
		<?php endfor ?>

	<?php else: /* « Previous  1 2 … 5 6 7 8 9 10 11 12 13 14 … 25 26  Next » */ ?>
		<? if (!isset($_GET['query'])){?>
			<a rel="ajax" id="page_1" href="#tag/<?=$this->tag->rid?>/<?=$sortby?>/<?=$this->order?>/1">1</a>
			<a rel="ajax" id="page_2" href="#tag/<?=$this->tag->rid?>/<?=$sortby?>/<?=$this->order?>/2">2</a>
		<?} else {?>
			<a rel="ajax" id="page_1" href="#search/<?=$_GET['query']?>/<?=$sortby?>/<?=$this->order?>/1">1</a>
			<a rel="ajax" id="page_2" href="#search/<?=$_GET['query']?>/<?=$sortby?>/<?=$this->order?>/2">2</a>
		<?}?>		
		&hellip;

		<?php for ($i = $current_page - 5; $i <= $current_page + 5; $i++): ?>
			<?php if ($i == $current_page): ?>
				<strong style="display: inline;"><?php echo $i ?></strong>
			<?php else: ?>
				<? if (!isset($_GET['query'])){?>
					<a rel="ajax" id="page_<?=$i?>" href="#tag/<?=$this->tag->rid?>/<?=$sortby?>/<?=$this->order?>/<?=$i?>"><?=$i?></a>
					<!--<a href="<?php echo str_replace('{page}', $i, $url) ?>"><?php echo $i ?></a>-->
				<?} else {?>
					<a rel="ajax" id="page_<?=$i?>" href="#search/<?=$_GET['query']?>/<?=$sortby?>/<?=$this->order?>/<?=$i?>"><?=$i?></a>
				<?}?>
			<?php endif ?>
		<?php endfor ?>
		&hellip;
		<? if (!isset($_GET['query'])){?>
			<a rel="ajax" id="page_<?=($total_pages-1)?>" href="#tag/<?=$this->tag->rid?>/<?=$sortby?>/<?=$this->order?>/<?=($total_pages-1)?>"><?=($total_pages-1)?></a>
			<a rel="ajax" id="page_<?=($total_pages-1)?>" href="#tag/<?=$this->tag->rid?>/<?=$sortby?>/<?=$this->order?>/<?=($total_pages)?>"><?=($total_pages)?></a>
		<?} else {?>
			<a rel="ajax" id="page_<?=($total_pages-1)?>" href="#search/<?=$_GET['query']?>/<?=$sortby?>/<?=$this->order?>/<?=($total_pages-1)?>"><?=($total_pages-1)?></a>
			<a rel="ajax" id="page_<?=($total_pages-1)?>" href="#search/<?=$_GET['query']?>/<?=$sortby?>/<?=$this->order?>/<?=($total_pages)?>"><?=($total_pages)?></a>
		<?}?>
	<?php endif ?>


	<?php if ($next_page): ?>
		<? if (!isset($_GET['query'])){?>
			<a rel="ajax" id="page_next_<?=$next_page?>" href="#tag/<?=$this->tag->rid?>/<?=$sortby?>/<?=$this->order?>/<?=$next_page?>"><?php echo Kohana::lang('pagination.next') ?>&nbsp;&raquo;</a>
		<?} else {?>
			<a rel="ajax" id="page_next_<?=$next_page?>" href="#search/<?=$_GET['query']?>/<?=$sortby?>/<?=$this->order?>/<?=$next_page?>"><?php echo Kohana::lang('pagination.next') ?>&nbsp;&raquo;</a>
		<?}?>
	<?php else: ?>
		<!--<?php echo Kohana::lang('pagination.next') ?>&nbsp;&raquo;-->
	<?php endif ?>
	<span class="loading paginationload hidden">&nbsp;</span>
</div>
