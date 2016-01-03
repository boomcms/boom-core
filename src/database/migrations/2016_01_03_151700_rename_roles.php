<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class RenameRoles extends Migration
{
    protected $roles = [
        'manage_people'                   => 'managePeople',
        'manage_assets'                   => 'manageAssets',
        'manage_templates'                => 'manageTemplates',
        'p_edit_page'                     => 'edit',
        'p_delete_page'                   => 'delete',
        'p_add_page'                      => 'add',
        'p_edit_feature_image'            => 'editFeature',
        'p_publish_page'                  => 'publish',
        'p_edit_page_navigation_basic'    => 'editNavBasic',
        'p_edit_page_navigation_advanced' => 'editNavAdvanced',
        'p_edit_page_search_basic'        => 'editSearchBasic',
        'p_edit_page_search_advanced'     => 'editSearchAdvanced',
        'p_edit_page_children_basic'      => 'editChildrenBasic',
        'p_edit_page_children_advanced'   => 'editChildrenAdvanced',
        'p_edit_page_admin'               => 'editAdmin',
        'p_edit_page_content'             => 'editContent',
        'p_edit_page_urls'                => 'editUrls',
        'p_edit_page_template'            => 'editTemplate',
        'manage_pages'                    => 'managePages',
        'manage_approvals'                => 'manageApprovals',
        'manage_robots'                   => 'manageRobotsTxt',
        'p_edit_disable_delete'           => 'editDeletable',
        'manage_settings'                 => 'manageSettings',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('tree')->default(false);
            $table->index('tree');
        });

        DB::statement('update roles set tree = true where name like "p_%"');

        foreach ($this->roles as $old => $new) {
            DB::table('roles')
                ->where('name', '=', $old)
                ->update([
                    'name' => $new,
                ]);
        }

        Schema::table('roles', function (Blueprint $table) {
            $table->string('name', 30)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('name', 100)->change();
        });

        foreach ($this->roles as $old => $new) {
            DB::table('roles')
                ->where('name', '=', $new)
                ->update([
                    'name' => $old,
                ]);
        }

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('tree');
        });
    }
}
