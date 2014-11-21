<?php

return array(
    'menu'    =>    array(
        'view_filename'        =>    'menu/boom',
        'items'            =>    array(
            'home'        =>    array(
                'title'        =>    'Site',
                'url'        =>    '/',
                'priority'    =>    1,
            ),
            'pages' => array(
                'title' => 'Pages',
                'url' => '/cms/pages',
                'priority' => 5,
                'role' => 'manage_pages',
            ),
            'approvals'    =>    array(
                'title'        =>    'Pages pending approval',
                'url'        =>    '/cms/approvals',
                'priority'    =>    10,
                'role'        =>    'manage_approvals',
            ),
            'templates'    =>    array(
                'title'        =>    'Templates',
                'url'        =>    '/cms/templates',
                'role'        =>    'manage_templates',
                'priority'    =>    6,
            ),
            'profile'        =>    array(
                'title'        =>    'Profile',
                'url'        =>    '/cms/profile',
                'priority'    =>    99,
            ),
            'logout'        =>    array(
                'title'        =>    'Logout',
                'url'        =>    '/cms/logout',
                'priority'    =>    100,
            ),
            'assets'    => array(
                'title'        =>    'Assets',
                'url'        =>    '/cms/assets',
                'role'        =>    'manage_assets',
                'priority'    =>    3,
            ),
            'people'    =>    array(
                'title'        =>    'People',
                'url'        =>    '/cms/people',
                'role'        =>    'manage_people',
                'priority'    =>    4,
            ),
        ),
    ),
    'htmlpurifier' => array(
        'AutoFormat.AutoParagraph' => true,
        'AutoFormat.RemoveEmpty.RemoveNbsp' => true,
        'AutoFormat.RemoveEmpty' => true,
        'AutoFormat.RemoveSpansWithoutAttributes' => true,
        'Core.RemoveInvalidImg' => false,
        'Cache.SerializerPath' => \Boom\Boom::instance()->getCacheDir(),
        'CSS.AllowedProperties' => array(),
        'URI.AllowedSchemes' => array (
            'http' => true,
            'https' => true,
            'mailto' => true,
            'tel' => true,
            'hoopdb' => true,
            'ftp' => true,
        ),
    ),
    'text_editor_toolbar' => array(
        'buttons' => array(
            'accept' => array('Accept changes', array('data-wysihtml5-action' => '', 'class' => 'action b-editor-accept')),
            'cancel' => array('Discard changes', array('data-wysihtml5-action' => '', 'class' => 'action b-editor-cancel')),
            'bold' => array('Make text bold (CTRL + B)', array('data-wysihtml5-command' => 'bold', 'class' => 'action')),
            'italic' => array('Make text italic (CTRL + I)', array('data-wysihtml5-command' => 'italic', 'class' => 'action')),
            'list' => array('Insert an unordered list', array('data-wysihtml5-command' => 'insertUnorderedList', 'class' => 'command')),
            'ol' => array('Insert an ordered list', array('data-wysihtml5-command' => 'insertOrderedList', 'class' => 'command')),
            'link' => array('Insert a link', array('data-wysihtml5-command' => 'createBoomLink', 'class' => 'command')),
            'asset' => array('Insert an asset', array('data-wysihtml5-command' => 'insertBoomAsset', 'class' => 'command')),
            'paragraph' => array('Insert paragraph', array('data-wysihtml5-command' => 'formatBlock', 'data-wysihtml5-command-value' => 'p', 'class' => 'command')),
            'h2' => array('Insert headline 2', array('data-wysihtml5-command' => 'formatBlock', 'data-wysihtml5-command-value' => 'h2', 'class' => 'command')),
            'h3' => array('Insert headline 3', array('data-wysihtml5-command' => 'formatBlock', 'data-wysihtml5-command-value' => 'h3', 'class' => 'command')),
            'blockquote' => array('Insert blockquote', array('data-wysihtml5-command' => 'insertBlockquote', 'class' => 'command')),
            'edit' => array('Edit link', array('class' => 'b-editor-link', 'data-wysihtml5-dialog' => 'createBoomLink', 'style' => 'display:none')),
            'cta' => array('Call to action', array('data-wysihtml5-command' => 'cta')),
        ),
        'button_sets' => array(
            'text' => array('accept', 'cancel'),
            'inline' => array('accept', 'cancel', 'bold', 'italic', 'link','edit'),
            'block' => array('accept', 'cancel', 'bold', 'italic', 'list', 'ol', 'link', 'edit', 'asset', 'paragraph', 'h2', 'h3', 'blockquote'),
        ),
    ),
);
