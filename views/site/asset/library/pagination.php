<div class="library-pagination clearfix">
	<ul>
		<? if ($current_page > 1): ?>
			<li class="previous"><a href="<?=$page->url() ?>/<?= $tag->pk() ?>/<?= $current_page - 1 ?>">Previous</a></li>
		<? endif; ?>

		<? for ($i = 1; $i <= $total_pages; $i++): ?>
			<li<?if ($i == $current_page):?> class="current"<? endif; ?>>
				<a href="<?=$page->url()?>/<?=$tag->pk() ?>/<?=$i?>"><?=$i?></a>
			</li>
		<? endfor; ?>

		<? if ($current_page < $total_pages): ?>
			<li class="previous"><a href="<?=$page->url()?>/<?=$tag->pk() ?>/<?=$current_page + 1?>">Next</a></li>
		<? endif; ?>
	</ul>
</div>