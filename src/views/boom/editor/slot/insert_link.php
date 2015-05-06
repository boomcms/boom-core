<div class="boom-tabs b-linkpicker">
	<ul>
		<li><a href="#b-linkpicker-add-internal">Select page</a></li>
		<li><a href="#b-linkpicker-add-external">Enter URL</a></li>
	</ul>

	<div id="b-linkpicker-add-internal">
		<input type="hidden" name="parent_id" value="">

        <p>
            Select a page from the CMS page tree.
        </p>

        <ul class="boom-tree">
            <li><a id="page_5" href="/" rel="5">Home</a></li>
        </ul>
	</div>

	<div id="b-linkpicker-add-external">
        <p>
            Manually enter an internal or external URL.
        </p>

		<form action="#">
			<select>
				<option value="http" selected="selected">Website</option>
				<option value="mailto">Email</option>
				<option value="tel">Phone number</option>
			</select>

			<input type="text" />
		</form>
	</div>
</div>
