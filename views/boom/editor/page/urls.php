<div id='b-page-urls' class="b-pagesettings">
	<section>
		<h1>Primary and secondary URLs</h1>
		<p>
			Below is a list of all URLs for the page.
		</p>
		<p>
			The highlighted URL indicates the page's primary URL.
			You may only have one primary URL for the page which cannot be deleted.
			All non-primary URLs will redirect to the primary URL.
			Click on a URL to make it the primary URL for the page.
		</p>
		<ul>
			<? foreach($urls as $url): ?>
				<li data-url="<?= $url->location ?>" data-id="<?= $url->id ?>" <? if ( (bool) $url->is_primary ): echo 'class="b-page-urls-primary"'; endif;?>>
					<label class="primary" for="is_primary_<?= $url->id ?>">/<?= $url->location ?></label>

					<span title="Remove URL" class="ui-icon ui-icon-remove-small b-urls-remove"></span>
					<?= Form::radio('is_primary', $url->location, (bool) $url->is_primary, array('id' => 'is_primary_' . $url->id, 'class' => 'ui-helper-hidden b-urls-primary')) ?>
				</li>
			<? endforeach; ?>
		</ul>
	</section>
	<section class='b-page-urls-short'>
		<h1>Short URL</h1>
		<p>This URL is automatically generated for use where the shortest URL possible is desirable such as when sharing on social media.</p>
		<p>When used the short URL will redirect to the page's primary URL</p>
		<p class='short-url'>
			<?= URL::site($page->short_url(), Request::current()) ?>
		</p>
	</section>
</div>
