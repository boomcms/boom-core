<?php

return [
    'driver' => 'eloquent',
    'model' => BoomCMS\Database\Models\Person::class,
    'table' => 'people',
    'password' => [
        'email'  => 'boomcms::email.password',
        'table'  => 'password_resets',
        'expire' => 60,
    ],

];
