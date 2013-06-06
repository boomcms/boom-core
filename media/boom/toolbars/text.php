<?
	$mode = isset( $_REQUEST[ 'mode' ] ) ? $_REQUEST[ 'mode' ] : 'block';
?>
<div id="wysihtml5-toolbar" class="toolbar toolbar-text">
	<div class="commands">
		<button id="b-editor-accept" data-wysihtml5-action class="action"><span class="b-accept"></span></button>
		<button id="b-editor-cancel" data-wysihtml5-action class="action"><span class="b-cancel"></span></button>
<?
	switch ( $mode ) {
		case 'text' :
		break;
		case 'inline' :
?>
		<button data-wysihtml5-command="bold" title="Make text bold (CTRL + B)" class="command"><span class="b-bold"></span></button>
		<button data-wysihtml5-command="italic" title="Make text italic (CTRL + I)" class="command"><span class="b-italic"></span></button>
		<button data-wysihtml5-command="createLink" title="Insert a link" class="command"><span class="b-link"></span></button>
		<button data-wysihtml5-command="insertSpeech" title="Insert speech" class="command"><span class="b-speech"></span></button>
<!--		<button data-wysihtml5-action="change_view" title="Show HTML" class="action"></button>-->
<?
		break;
		default :
?>
		<button data-wysihtml5-command="bold" title="Make text bold (CTRL + B)" class="command"><span class="b-bold"></span></button>
		<button data-wysihtml5-command="italic" title="Make text italic (CTRL + I)" class="command"><span class="b-italic"></span></button>
		<button data-wysihtml5-command="insertUnorderedList" title="Insert an unordered list" class="command"><span class="b-list"></span></button>
		<button data-wysihtml5-command="insertOrderedList" title="Insert an ordered list" class="command"><span class="b-ol"></span></button>
		<button data-wysihtml5-command="insertHorizontalRule" title="Insert horizontal rule" class="command"><span class="b-hr"></span></button>
		<button data-wysihtml5-command="createLink" title="Insert a link" class="command"><span class="b-link"></span></button>
		<button data-wysihtml5-command="insertImage" title="Insert an asset" class="command"><span class="b-asset"></span></button>
		<button data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="p" title="Insert paragraph" class="command"><span class="b-paragraph"></span></button>
		<button data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="h2" title="Insert headline 2" class="command"><span class="b-h2"></span></button>
		<button data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="h3" title="Insert headline 3" class="command"><span class="b-h3"></span></button>
		<button data-wysihtml5-command="indent" title="Insert blockquote" class="command"><span class="b-blockquote"></span></button>
		<button data-wysihtml5-command="insertSpeech" title="Insert speech" class="command"><span class="b-speech"></span></button>
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