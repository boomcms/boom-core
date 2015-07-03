<div id="b-linkpicker-container">
	<div class="boom-tabs b-linkpicker">
		<ul>
			<li><a href="#b-linkpicker-add-internal">Select page</a></li>
			<li><a href="#b-linkpicker-add-external">Enter URL</a></li>
			<li><a href="#b-linkpicker-text">Link text</a></li>
		</ul>

		<div id="b-linkpicker-add-internal">
			<p>Select a page from the CMS page tree.</p>
			<ul class="boom-tree"></ul>
		</div>

		<div id="b-linkpicker-add-external">
			<p>
				Manually enter an internal or external URL, or type the title of a page to search for a URL
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
		
		<div id="b-linkpicker-text">
			<p>
				Set the text for the link.
			</p>

			<form action="#">
				<input type="text" name="link_text" />
			</form>
		</div>
	</div>
</div>