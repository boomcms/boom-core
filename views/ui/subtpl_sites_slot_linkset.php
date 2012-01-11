<div class="sledge-tabs" id="test">
	<ul>
		<li><a href="#sledge-chunk-linkset-links">Edit links</a></li>
		<li><a href="#sledge-chunk-linkset-addlink">Add link</a></li>
	</ul>
	<div id="sledge-chunk-linkset-links">
		<div id="sledge-chunk-linkset-urls-valid">
			<div class="ui-widget">
				<div class="ui-state-highlight ui-corner-all"> 
					<p style="margin: .5em;">
						<span style="float: left; margin-right: 0.3em; margin-top:-.2em" class="ui-icon ui-icon-info"></span>
						Drag the links to re-arrange. Click on the X to remove.
					</p> 
				</div>
			</div>
			<br />
			<ul class="sledge-tree sledge-tree-remove sledge-sortable sledge-chunk-linkset-links-set">
			</ul>
		</div>
		<div id="sledge-chunk-linkset-urls-invalid" class="ui-helper-hidden">
			<div class="ui-state-highlight ui-corner-all"> 
				<p style="margin: .5em;">
					<span style="float: left; margin-right: 0.3em; margin-top:-.2em" class="ui-icon ui-icon-info"></span>
					You have not added any links yet. Click on 'Add link' to add some links to this set.
				</p> 
			</div>
		</div>
			
	</div>
	<div id="sledge-chunk-linkset-addlink" class="sledge-tabs">
		<ul>
			<li><a href="#sledge-chunk-linkset-addlink-internal">Internal link</a></li>
			<li><a href="#sledge-chunk-linkset-addlink-external">External link</a></li>
		</ul>
		<div id="sledge-chunk-linkset-addlink-internal">
			<div class="ui-widget">
				<div class="ui-state-highlight ui-corner-all"> 
					<p style="margin: .5em;">
						<span style="float: left; margin-right: 0.3em; margin-top:-.2em" class="ui-icon ui-icon-info"></span>
						Click on a page to add it to the linkset.
					</p> 
				</div>
			</div>
			<br />
			<ul class="sledge-tree sledge-chunk-linkset-internal-links">
				<?
					$r = new Recursion_Page_Tree;
					$r->recurse(O::fa('page')->find_by_title('Site 1'), O::f('site_page')->get_homepage()->rid, true, false, false, false, false, false, false, false);
				?>
			</ul>
		</div>
		<div id="sledge-chunk-linkset-addlink-external">
			<form id="sledge-chunk-linkset-addlink-external-form" action="#">
				<table width="100%">
					<tr>
						<td>URL</td>
						<td><input type="text" id="sledge-chunk-linkset-addlink-external-url" class="sledge-input" value="http://" /></td>
					</tr>
					<tr>
						<td>Copy</td>
						<td><input type="text" id="sledge-chunk-linkset-addlink-external-copy" class="sledge-input"></td>
					</tr>
				</table>
			</form>

			<br />

			<button id="sledge-chunk-linkset-addlink-external-button" class="sledge-button sledge-button-addlink">
				Add link
			</button>
		</div>
	</div>
</div>
