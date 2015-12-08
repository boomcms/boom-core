<?php

use BoomCMS\Database\Models\AssetVersion;
use BoomCMS\Support\File;
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
            $v->extension = File::extensionFromMimetype($v->mimetype);
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
