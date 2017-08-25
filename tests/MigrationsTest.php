<?php

namespace BoomCMS\Tests;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use PDO;

class MigrationsTest extends AbstractTestCase
{
    public function testMigrationsComplete()
    {
        $pdo = new PDO(env('DB_DRIVER').':host='.env('DB_HOST'), env('DB_USERNAME'), env('DB_PASSWORD'));
        $pdo->exec('drop database if exists '.env('DB_DATABASE'));
        $pdo->exec('create database '.env('DB_DATABASE'));
        $pdo->exec('set global innodb_large_prefix = "On"');

        $app = App::getFacadeRoot();
        $app['migration.repository']->createRepository();
        $app['migrator']->run(realpath(__DIR__.'/../src/database/migrations'));

        $pdo->exec('drop database '.env('DB_DATABASE'));
    }
}
