<?php

use BoomCMS\Database\Models\AssetVersion;
use BoomCMS\Support\Helpers\Asset as AssetHelper;
use Illuminate\Database\Migrations\Migration;

class SetMissingExtensionOfImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $versions = AssetVersion::whereNull('extension')->get();

        foreach ($versions as $v) {
            $v->extension = AssetHelper::extensionFromMimetype($v->mimetype);
            $v->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
