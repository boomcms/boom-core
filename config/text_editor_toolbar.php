<?php

return array(
	'buttons' => array(
		'accept' => array('Accept changes', array('data-wysihtml5-action' => '', 'class' => 'action', 'id' => 'b-editor-accept')),
		'cancel' => array('Discard changes', array('data-wysihtml5-action' => '', 'class' => 'action', 'id' => 'b-editor-cancel')),
		'bold' => array('Make text bold (CTRL + B)', array('data-wysihtml5-command' => 'bold', 'class' => 'action')),
		'italic' => array('Make text italic (CTRL + I)', array('data-wysihtml5-command' => 'italic', 'class' => 'action')),
		'list' => array('Insert an unordered list', array('data-wysihtml5-command' => 'insertUnorderedList', 'class' => 'command')),
		'ol' => array('Insert an ordered list', array('data-wysihtml5-command' => 'insertOrderedList', 'class' => 'command')),
		'hr' => array('Insert horizontal rule', array('data-wysihtml5-command' => 'insertHorizontalRule', 'class' => 'command')),
		'link' => array('Insert a link', array('data-wysihtml5-command' => 'createBoomLink', 'class' => 'command')),
		'asset' => array('Insert an asset', array('data-wysihtml5-command' => 'insertBoomAsset', 'class' => 'command')),
		'paragraph' => array('Insert paragraph', array('data-wysihtml5-command' => 'formatBlock', 'data-wysihtml5-command-value' => 'p', 'class' => 'command')),
		'h2' => array('Insert headline 2', array('data-wysihtml5-command' => 'formatBlock', 'data-wysihtml5-command-value' => 'h2', 'class' => 'command')),
		'h3' => array('Insert headline 3', array('data-wysihtml5-command' => 'formatBlock', 'data-wysihtml5-command-value' => 'h3', 'class' => 'command')),
		'blockquote' => array('Insert blockquote', array('data-wysihtml5-command' => 'formatBlock', 'data-wysihtml5-command-value' => 'blockquote', 'class' => 'command')),
		'edit' => array('Edit link', array('id' => 'b-editor-link', 'data-wysihtml5-dialog' => 'createBoomLink', 'style' => 'display:none')),
	),
	'button_sets' => array(
		'text' => array('accept', 'cancel'),
		'inline' => array('accept', 'cancel', 'bold', 'italic', 'link',' edit'),
		'block' => array('accept', 'cancel', 'bold', 'italic', 'list', 'ol', 'hr', 'link', 'asset', 'paragraph', 'h2', 'h3', 'blockquote', 'edit'),
	),
);