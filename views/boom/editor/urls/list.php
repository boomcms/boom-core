<div id='b-pagesettings-urls' class="b-pagesettings">
	<div>
		<h1>Primary URL</h1>
		<p>
			<?= $page->url() ?>
		</p>
	</div>
	<div>
		<h1>All URLs</h1>
		<ul>
			<? foreach($page->urls->order_by('location', 'asc')->find_all() as $url): ?>
				<li data-url="<?= $url->location ?>" data-id="<?= $url->id ?>" <? if ( (bool) $url->is_primary ): echo 'class="ui-state-active"'; endif;?>>
					<?= $url->location ?>
					<?= Form::radio('is_primary', $url->location, (bool) $url->is_primary, array('id' => 'is_primary_' . $url->id, 'class' => 'b-urls-primary')) ?>
					<label for="is_primary_<?= $url->id ?>">√</label>
					<?= Form::checkbox("redirect_" . $url->id, 1, (bool) $url->redirect, array('id' => 'redirect_' . $url->id,'class' => 'b-urls-redirect')) ?>
					<label for="redirect_<?= $url->id ?>"><span class="on">R</span> <span class="off">V</span></label>
				</li>
			<? endforeach; ?>
		</ul>
	</div>
	<div>
		<h1>Short URL</h1>
		<p>
			<?= URL::site($page->short_url()) ?>
		</p>
	</div>
</div>