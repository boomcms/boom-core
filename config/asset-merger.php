<?php

return array(
    'load_paths' => array(
        Assets::JAVASCRIPT => array(__DIR__ . '/../media/boom/js/', __DIR__ . '/../bower_components/'),
        Assets::STYLESHEET => array(__DIR__.'/../media/boom/css/', __DIR__ . '/../bower_components/'),
    ),
    'dev' => Boom\Boom::instance()->getEnvironment()->isDevelopment(),
);
