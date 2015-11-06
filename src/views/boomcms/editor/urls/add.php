<h1>Add URL</h1>
<h2>Some points to remember when creating a URL</h2>
<ul>
	<li>URLs should contain only lower-case letters, numbers, or hyphens eg /course-programe-news-august13</li>
	<li>No spaces or punctuation should be used.</li>
	<li>Separate words with a hyphen eg /case-studies</li>
	<li>It should be clear from the URL what the content of the page is. Users who follow a URL to your page shouldn't be surprised by the content when they get there.</li>
	<li>Use only keywords in the URL. Remove linking words  – eg  'and', 'the' –, that are not descriptive.</li>
</ul>

<form id="b-form-addurl" name="form-addurl">
	<input type='hidden' name='page' value='<?= $page->getId() ?>' />

	<label>New URL
		<input type='text' name='url' id='url' />
	</label>
</form>
