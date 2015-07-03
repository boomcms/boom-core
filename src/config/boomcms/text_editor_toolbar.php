<?php

return [
    'text_editor_toolbar' => [
        'buttons' => [
            'check' => ['Accept changes', ['data-wysihtml5-action' => '', 'class' => 'action b-editor-accept']],
            'times' => ['Discard changes', ['data-wysihtml5-action' => '', 'class' => 'action b-editor-cancel']],
            'bold' => ['Make text bold (CTRL + B)', ['data-wysihtml5-command' => 'bold', 'class' => 'action']],
            'italic' => ['Make text italic (CTRL + I)', ['data-wysihtml5-command' => 'italic', 'class' => 'action']],
            'list-ul' => ['Insert an unordered list', ['data-wysihtml5-command' => 'insertUnorderedList', 'class' => 'command']],
            'list-ol' => ['Insert an ordered list', ['data-wysihtml5-command' => 'insertOrderedList', 'class' => 'command']],
            'link' => ['Insert a link', ['data-wysihtml5-command' => 'createBoomLink', 'class' => 'command']],
            'paperclip' => ['Insert an asset', ['data-wysihtml5-command' => 'insertBoomAsset', 'class' => 'command']],
            'paragraph' => ['Insert paragraph', ['data-wysihtml5-command' => 'formatBlock', 'data-wysihtml5-command-value' => 'p', 'class' => 'command']],
            'h2' => ['Insert headline 2', ['data-wysihtml5-command' => 'formatBlock', 'data-wysihtml5-command-value' => 'h2', 'class' => 'command']],
            'h3' => ['Insert headline 3', ['data-wysihtml5-command' => 'formatBlock', 'data-wysihtml5-command-value' => 'h3', 'class' => 'command']],
            'quote-left' => ['Insert blockquote', ['data-wysihtml5-command' => 'insertBlockQuote', 'class' => 'command']],
            'exclamation' => ['Call to action', ['data-wysihtml5-command' => 'cta']],
            'superscript' => ['Superscript', ['data-wysihtml5-command' => 'superscript', 'class' => 'command']],
            'subscript' => ['Subscript', ['data-wysihtml5-command' => 'subscript', 'class' => 'command']],
            'minus' => ['Insert horizontal rule', ['data-wysihtml5-command' => 'insertHorizontalRule', 'class' => 'command']],
			'table' => ['Insert table', ['class' => 'command b-editor-table']],
		],
        'button_sets' => [
            'text' => [['check', 'times']],
            'inline' => [['check', 'times'], ['bold', 'italic'], ['link']],
            'block' => [['check', 'times'], ['h2', 'h3', 'paragraph', 'bold', 'italic', 'list-ul', 'list-ol', 'quote-left', 'exclamation'], ['link', 'paperclip', 'minus', 'table']],
        ],
    ],
];