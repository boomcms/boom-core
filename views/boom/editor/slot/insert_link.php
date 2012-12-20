<div id="boom-chunk-linkset-addlink" class="boom-tabs">
	<ul>
		<li><a href="#boom-chunk-linkset-addlink-internal">Internal link</a></li>
		<li><a href="#boom-chunk-linkset-addlink-external">External link</a></li>
	</ul>
	<div id="boom-chunk-linkset-addlink-internal">
		<ul class="boom-chunk-linkset-internal-links">
			<?= Request::factory('cms/page/tree')->execute() ?>
		</ul>
	</div>
	<div id="boom-chunk-linkset-addlink-external">
		<form id="boom-chunk-linkset-addlink-external-form" action="#">
			<table width="100%">
				<tr>
					<td>URL</td>
					<td><input type="text" id="boom-chunk-linkset-addlink-external-url" class="boom-input" value="http://" /></td>
				</tr>
			</table>
		</form>
	</div>
</div>