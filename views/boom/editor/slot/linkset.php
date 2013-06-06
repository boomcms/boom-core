<div class="boom-tabs" id="test">
	<ul>
		<li><a href="#boom-chunk-linkset-links">Edit links</a></li>
		<li><a href="#boom-chunk-linkset-addlink">Add link</a></li>
	</ul>
	<div id="boom-chunk-linkset-links">
		<div id="boom-chunk-linkset-urls-valid">
			<div class="ui-widget">
				<div class="ui-state-highlight ui-corner-all">
					<p style="margin: .5em;">
						<span style="float: left; margin-right: 0.3em; margin-top:-.2em" class="ui-icon ui-icon-info"></span>
						Drag the links to re-arrange. Click on the X to remove.
					</p>
				</div>
			</div>
			<br />
			<ul class="boom-tree boom-tree-noborder boom-sortable boom-chunk-linkset-links-set">
			</ul>
		</div>
		<div id="boom-chunk-linkset-urls-invalid" class="ui-helper-hidden">
			<div class="ui-state-highlight ui-corner-all">
				<p style="margin: .5em;">
					<span style="float: left; margin-right: 0.3em; margin-top:-.2em" class="ui-icon ui-icon-info"></span>
					You have not added any links yet. Click on 'Add link' to add some links to this set.
				</p>
			</div>
		</div>

	</div>
	<div id="boom-chunk-linkset-addlink" class="boom-tabs">
		<ul>
			<li><a href="#boom-chunk-linkset-addlink-internal">Internal link</a></li>
			<li><a href="#boom-chunk-linkset-addlink-external">External link</a></li>
		</ul>
		<div id="boom-chunk-linkset-addlink-internal">
			<div class="ui-widget">
				<div class="ui-state-highlight ui-corner-all">
					<p style="margin: .5em;">
						<span style="float: left; margin-right: 0.3em; margin-top:-.2em" class="ui-icon ui-icon-info"></span>
						Click on a page to add it to the linkset.
					</p>
				</div>
			</div>
			<br />

				<input type="hidden" name="parent_id" value="<?=$page->mptt->parent_id?>">
				<div class="boom-chunk-linkset-internal-links">
					<ul class="boom-tree">
						<li><a id="page_5" href="/" rel="5">Home</a></li>
					</ul>
				</div>

		</div>
		<div id="boom-chunk-linkset-addlink-external">
			<form id="boom-chunk-linkset-addlink-external-form" action="#">
				URL
				<input type="text" id="boom-chunk-linkset-addlink-external-url" class="boom-input" value="http://" />

				Copy
				<input type="text" id="boom-chunk-linkset-addlink-external-copy" class="boom-input">
			</form>

			<br />

			<button id="boom-chunk-linkset-addlink-external-button" class="boom-button boom-button-addlink">
				Add link
			</button>
		</div>
	</div>
</div>
