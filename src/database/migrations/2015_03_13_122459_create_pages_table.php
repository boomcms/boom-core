<?php

use BoomCMS\Database\Models\Page;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sequence')->unsigned()->nullable()->default(0)->index('page_sequence');
            $table->boolean('visible')->nullable()->default(0);
            $table->integer('visible_from')->unsigned()->nullable();
            $table->integer('visible_to')->unsigned()->default(0);
            $table->string('internal_name', 64)->nullable()->unique('pages_internal_name');
            $table->boolean('external_indexing')->default(1)->index('pages_external_indexing');
            $table->boolean('internal_indexing')->default(1)->index('pages_internal_indexing');
            $table->boolean('visible_in_nav')->default(1);
            $table->boolean('visible_in_nav_cms')->default(1);
            $table->boolean('children_visible_in_nav')->default(1);
            $table->boolean('children_visible_in_nav_cms')->default(1);
            $table->boolean('children_template_id')->nullable();
            $table->string('children_url_prefix', 2048)->nullable();
            $table->boolean('children_ordering_policy')->nullable();
            $table->boolean('grandchild_template_id')->nullable();
            $table->string('keywords')->nullable();
            $table->text('description', 65535)->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('created_time')->unsigned()->nullable();
            $table->string('primary_uri', 2048)->nullable()->index(DB::raw('pages_primary_uri(2048)'));
            $table->integer('feature_image_id')->unsigned()->nullable()->index('pages_feature_image_id');
            $table->index(['visible', 'visible_from', 'visible_to', 'visible_in_nav'], 'pages_sitelist');
            $table->index(['visible_in_nav_cms', 'visible_from'], 'pages_cmslist');
            $table->integer('parent_id')->unsigned()->nullable();
            $table->foreign('parent_id')->references('id')->on('pages')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->boolean('disable_delete')->default(false);

            $table->softDeletes();
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->tinyinteger(Page::ATTR_ADD_BEHAVIOUR)->default(1);
            $table->tinyinteger(Page::ATTR_CHILD_ADD_BEHAVIOUR)->default(1);
            $table->index(['deleted_at', 'parent_id', 'visible']);
            $table->boolean(Page::ATTR_ENABLE_ACL)->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pages');
    }
}
