<div id="sledge-chunk-linkset-addlink" class="sledge-tabs">
	<ul>
		<li><a href="#sledge-chunk-linkset-addlink-internal">Internal link</a></li>
		<li><a href="#sledge-chunk-linkset-addlink-external">External link</a></li>
	</ul>
	<div id="sledge-chunk-linkset-addlink-internal">
		<ul class="sledge-chunk-linkset-internal-links">
			<?= Request::factory('cms/page/tree')->execute() ?>
		</ul>
	</div>
	<div id="sledge-chunk-linkset-addlink-external">
		<form id="sledge-chunk-linkset-addlink-external-form" action="#">
			<table width="100%">
				<tr>
					<td>URL</td>
					<td><input type="text" id="sledge-chunk-linkset-addlink-external-url" class="sledge-input" value="http://" /></td>
				</tr>
			</table>
		</form>
	</div>
</div>