<?
	$mode = isset( $mode ) ? $mode : 'block';
?>
<div id="wysihtml5-toolbar" class="toolbar">
	<div class="commands">
		<button id="b-editor-accept" data-wysihtml5-action class="action">Save</button>
		<button id="b-editor-cancel" data-wysihtml5-action class="action">Cancel</button>
<?
	switch ( $mode ) {
		case 'text' :
		break;
		case 'inline' :
?>
		<button data-wysihtml5-command="bold" title="Make text bold (CTRL + B)" class="command">bold</button>
		<button data-wysihtml5-command="italic" title="Make text italic (CTRL + I)" class="command">italic</button>
		<button data-wysihtml5-command="createLink" title="Insert a link" class="command">link</button>
		<button data-wysihtml5-command="insertSpeech" title="Insert speech" class="command">speech</button>
		<button data-wysihtml5-action="change_view" title="Show HTML" class="action">HTML</button>
<?		
		break;
		default :
?>
		<button data-wysihtml5-command="bold" title="Make text bold (CTRL + B)" class="command">bold</button>
		<button data-wysihtml5-command="italic" title="Make text italic (CTRL + I)" class="command">italic</button>
		<button data-wysihtml5-command="insertUnorderedList" title="Insert an unordered list" class="command">UL</button>
		<button data-wysihtml5-command="insertOrderedList" title="Insert an ordered list" class="command">OL</button>
		<button data-wysihtml5-command="insertHorizontalRule" title="Insert horizontal rule" class="command">HR</button>
		<button data-wysihtml5-command="createLink" title="Insert a link" class="command">link</button>
		<button data-wysihtml5-command="insertImage" title="Insert an image" class="command">asset</button>
		<button data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="p" title="Insert paragraph" class="command">P</button>
		<button data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="h2" title="Insert headline 2" class="command">H2</button>
		<button data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="h3" title="Insert headline 3" class="command">H3</button>
		<button data-wysihtml5-command="indent" title="Insert blockquote" class="command">blockquote</button>
		<button data-wysihtml5-command="insertSpeech" title="Insert speech" class="command">speech</button>
		<button data-wysihtml5-action="change_view" title="Show HTML" class="action">HTML</button>
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