<?php

use BoomCMS\Page\Finder;
use BoomCMS\Support\Facades\Chunk;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateTableForSearchText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_texts', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('page_vid')
                ->unsigned()
                ->nullable()
                ->references('id')
                ->on('page_versions')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->integer('page_id')
                ->unsigned()
                ->references('id')
                ->on('pages')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table->integer('embargoed_until')->unsigned()->nullable();
            $table->string('title', 75)->nullable();
            $table->string('standfirst', '255')->null();
            $table->longText('text')->nullable();
        });

        DB::statement('ALTER TABLE search_texts ENGINE = "MyISAM"');
        DB::statement('CREATE FULLTEXT INDEX search_texts_title on search_texts(title)');
        DB::statement('CREATE FULLTEXT INDEX search_texts_standfirst on search_texts(standfirst)');
        DB::statement('CREATE FULLTEXT INDEX search_texts_text on search_texts(text)');
        DB::statement('CREATE FULLTEXT INDEX search_texts_all on search_texts(title, standfirst, text)');
        DB::statement('ALTER TABLE chunk_texts drop index text_fulltext');
        DB::statement('ALTER TABLE page_versions drop index title_fulltext');

        $finder = new Finder\Finder();
        $finder->addFilter(new Finder\VisibleInSiteSearch());
        $pages = $finder->findAll();

        foreach ($pages as $p) {
            DB::table('search_texts')
                ->insert([
                    'page_id'         => $p->getId(),
                    'embargoed_until' => $p->getCurrentVersion()->getEmbargoedUntil()->getTimestamp(),
                    'page_vid'        => $p->getCurrentVersion()->getId(),
                    'title'           => $p->getTitle(),
                    'standfirst'      => Chunk::get('text', 'standfirst', $p)->text(),
                    'text'            => strip_tags(Chunk::get('text', 'bodycopy', $p)->text()),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('search_texts');
    }
}
