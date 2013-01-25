<?
	$mode = isset( $_REQUEST[ 'mode' ] ) ? $_REQUEST[ 'mode' ] : 'block';
?>
<div id="wysihtml5-toolbar" class="toolbar">
	<div class="commands">
		<button id="b-editor-accept" data-wysihtml5-action class="action b-accept"></button>
		<button id="b-editor-cancel" data-wysihtml5-action class="action b-cancel"></button>
<?
	switch ( $mode ) {
		case 'text' :
		break;
		case 'inline' :
?>
		<button data-wysihtml5-command="bold" title="Make text bold (CTRL + B)" class="command b-bold"></button>
		<button data-wysihtml5-command="italic" title="Make text italic (CTRL + I)" class="command b-italic"></button>
		<button data-wysihtml5-command="createLink" title="Insert a link" class="command b-link"></button>
		<button data-wysihtml5-command="insertSpeech" title="Insert speech" class="command b-speech"></button>
		<button data-wysihtml5-action="change_view" title="Show HTML" class="action"></button>
<?
		break;
		default :
?>
		<button data-wysihtml5-command="bold" title="Make text bold (CTRL + B)" class="command b-bold"></button>
		<button data-wysihtml5-command="italic" title="Make text italic (CTRL + I)" class="command b-italic"></button>
		<button data-wysihtml5-command="insertUnorderedList" title="Insert an unordered list" class="command b-list"></button>
		<button data-wysihtml5-command="insertOrderedList" title="Insert an ordered list" class="command b-ol"></button>
		<button data-wysihtml5-command="insertHorizontalRule" title="Insert horizontal rule" class="command b-hr"></button>
		<button data-wysihtml5-command="createLink" title="Insert a link" class="command b-link"></button>
		<button data-wysihtml5-command="insertImage" title="Insert an image" class="command b-asset"></button>
		<button data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="p" title="Insert paragraph" class="command b-paragraph"></button>
		<button data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="h2" title="Insert headline 2" class="command b-h2"></button>
		<button data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="h3" title="Insert headline 3" class="command b-h3"></button>
		<button data-wysihtml5-command="indent" title="Insert blockquote" class="command b-blockquote"></button>
		<button data-wysihtml5-command="insertSpeech" title="Insert speech" class="command b-speech"></button>
<!--		<button data-wysihtml5-action="change_view" title="Show HTML" class="action"></button>-->
<?
	}
?>
	</div>
	<div data-wysihtml5-dialog="createLink" style="display: none;">
		<label>
			Link:
			<input data-wysihtml5-dialog-field="href" value="http://">
		</label>
	</div>

	<div data-wysihtml5-dialog="insertImage" style="display: none;">
		<label>
			Image:
			<input data-wysihtml5-dialog-field="src" value="http://">
		</label>
	</div>
</div>