<div id='b-pagesettings-links' class="b-pagesettings">
	<div>
		<h1>Primary Link</h1>
		<p>
			<?= $page->link() ?>
		</p>
	</div>
	<div>
		<h1>All links</h1>
		<ul class='boom-tree'>
			<? foreach($page->links->order_by('location', 'asc')->find_all() as $link): ?>
				<li data-link="<?= $link->location ?>" data-id="<?= $link->id ?>">
					<?= $link->location ?>
					<?= Form::radio('is_primary', $link->location, (bool) $link->is_primary, array('class' => 'b-links-primary')) ?>
					<?= Form::checkbox("redirect_" . $link->id, 1, (bool) $link->redirect, array('class' => 'b-links-redirect')) ?>
				</li>
			<? endforeach; ?>
		</ul>
	</div>
	<div>
		<h1>Short Link</h1>
		<p>
			<?= URL::site($page->short_link()) ?>
		</p>
	</div>
</div>