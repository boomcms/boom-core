<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class PopulateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("INSERT INTO `roles` VALUES (2,'manage_people','People manager - view and edit'),(4,'manage_assets','Asset manager - view and edit'),(7,'p_edit_page','Page - edit'),(8,'p_delete_page','Page - delete'),(9,'p_add_page','Page - add'),(34,'p_edit_feature_image','Edit page - feature image'),(49,'p_edit_page_template','Edit page - template'),(67,'p_publish_page','Page - publish'),(70,'p_edit_page_navigation_basic','Edit settings - navigation (basic)'),(71,'p_edit_page_navigation_advanced','Edit settings - navigation (advanced)'),(72,'p_edit_page_search_basic','Edit settings - search (basic)'),(73,'p_edit_page_search_advanced','Edit settings - search (advanced)'),(74,'p_edit_page_children_basic','Edit settings - children (basic)'),(75,'p_edit_page_children_advanced','Edit settings - children (advanced)'),(76,'p_edit_page_admin','Edit settings - admin'),(77,'p_edit_page_content','Edit page - content'),(78,'p_edit_page_urls','Edit page - URLs'),(79,'manage_pages','Page manager - view and edit'),(81,'manage_templates','View the template manager'),(82,'manage_approvals','View the list of pages pending approval')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('truncate roles');
    }
}
