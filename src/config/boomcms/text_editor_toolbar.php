<?php

return [
    'text_editor_toolbar' => [
        'buttons' => [
            'accept' => ['Accept changes', ['data-wysihtml5-action' => '', 'class' => 'action b-editor-accept']],
            'cancel' => ['Discard changes', ['data-wysihtml5-action' => '', 'class' => 'action b-editor-cancel']],
            'bold' => ['Make text bold (CTRL + B)', ['data-wysihtml5-command' => 'bold', 'class' => 'action']],
            'italic' => ['Make text italic (CTRL + I)', ['data-wysihtml5-command' => 'italic', 'class' => 'action']],
            'list' => ['Insert an unordered list', ['data-wysihtml5-command' => 'insertUnorderedList', 'class' => 'command']],
            'ol' => ['Insert an ordered list', ['data-wysihtml5-command' => 'insertOrderedList', 'class' => 'command']],
            'link' => ['Insert a link', ['data-wysihtml5-command' => 'createBoomLink', 'class' => 'command']],
            'asset' => ['Insert an asset', ['data-wysihtml5-command' => 'insertBoomAsset', 'class' => 'command']],
            'paragraph' => ['Insert paragraph', ['data-wysihtml5-command' => 'formatBlock', 'data-wysihtml5-command-value' => 'p', 'class' => 'command']],
            'h2' => ['Insert headline 2', ['data-wysihtml5-command' => 'formatBlock', 'data-wysihtml5-command-value' => 'h2', 'class' => 'command']],
            'h3' => ['Insert headline 3', ['data-wysihtml5-command' => 'formatBlock', 'data-wysihtml5-command-value' => 'h3', 'class' => 'command']],
            'blockquote' => ['Insert blockquote', ['data-wysihtml5-command' => 'formatBlock', 'data-wysihtml5-command-value' => 'blockquote', 'class' => 'command']],
            'edit' => ['Edit link', ['class' => 'b-editor-link', 'data-wysihtml5-dialog' => 'createBoomLink', 'style' => 'display:none']],
            'cta' => ['Call to action', ['data-wysihtml5-command' => 'cta']],
            'sup' => ['Superscript', ['data-wysihtml5-command' => 'insertSuperscript', 'class' => 'command']],
            'sub' => ['Subscript', ['data-wysihtml5-command' => 'insertSubscript', 'class' => 'command']],
            'hr' => ['Insert horizontal rule', ['data-wysihtml5-command' => 'insertHorizontalRule', 'class' => 'command']],
        ],
        'button_sets' => [
            'text' => [['accept', 'cancel']],
            'inline' => [['accept', 'cancel'], ['bold', 'italic'], ['link','edit']],
            'block' => [['accept', 'cancel'], ['h2', 'h3', 'paragraph', 'bold', 'italic', 'list', 'ol', 'blockquote', 'cta'], ['link', 'edit', 'asset', 'hr']],
        ],
    ],
];