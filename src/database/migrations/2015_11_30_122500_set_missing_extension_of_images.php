<?php

use BoomCMS\Database\Models\AssetVersion;
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
        $versions = AssetVersion::whereNull('extension')
            ->where('type', '=', 'image')
            ->get();

        foreach ($versions as $v) {
            $v->extension = image_type_to_extension($v->mimetype, false);
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
