<div id='b-pagesettings-urls' class="b-pagesettings">
	<div>
		<h2>URLs</h2>
		<ul class="boom-tree">
			<? foreach($page->urls->order_by('location', 'asc')->find_all() as $url): ?>
				<li data-url="<?= $url->location ?>" data-id="<?= $url->id ?>" <? if ( (bool) $url->is_primary ): echo 'class="ui-state-active"'; endif;?>>
					<?= $url->location ?>
					<?= Form::radio('is_primary', $url->location, (bool) $url->is_primary, array('id' => 'is_primary_' . $url->id, 'class' => 'b-urls-primary')) ?>
					<span class="tools">
						<label for="is_primary_<?= $url->id ?>">✔</label>
						<?
						if ( !$url->is_primary ):
						?>
							<?= Form::checkbox("redirect_" . $url->id, 1, (bool) $url->redirect, array('id' => 'redirect_' . $url->id,'class' => 'b-urls-redirect')) ?>
							<label for="redirect_<?= $url->id ?>">
								<span class="on">R</span> 
								<span class="off">V</span>
							</label>
							<button class="boom-button b-urls-remove">X</button>
						<?
						endif;
						?>
					</span>
					
				</li>
			<? endforeach; ?>
		</ul>
	</div>
	<div>
		<h2>Primary URL</h2>
		<p>
			<?= $page->url() ?>
		</p>
	</div>
	<div>
		<h2>Short URL</h2>
		<p>
			<?= URL::site($page->short_url()) ?>
		</p>
	</div>
</div>