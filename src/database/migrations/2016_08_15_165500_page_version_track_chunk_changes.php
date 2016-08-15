<?php

use BoomCMS\Database\Models\PageVersion;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class PageVersionTrackChunkChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('page_versions', function (Blueprint $table) {
            $table->string(PageVersion::ATTR_CHUNK_TYPE, 15);
            $table->integer(PageVersion::ATTR_CHUNK_ID)->unsigned();
        });

        $versions = PageVersion::all();
        $types = ['text', 'feature', 'asset', 'slideshow', 'linkset', 'link', 'location', 'timestamp', 'html', 'calendar', 'library'];

        foreach ($versions as $version) {
            foreach ($types as $type) {
                $className = 'BoomCMS\Database\Models\Chunk\\'.ucfirst($type);

                $chunk = (new $className())
                    ->where('page_vid', $version->getId())
                    ->first();

                if ($chunk) {
                    $version->{PageVersion::ATTR_CHUNK_TYPE} = $type;
                    $version->{PageVersion::ATTR_CHUNK_ID} = $chunk->id;
                    $version->save();

                    break;
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('page_versions', function (Blueprint $table) {
            $table->dropColumn(PageVersion::ATTR_CHUNK_TYPE);
            $table->dropColumn(PageVersion::ATTR_CHUNK_ID);
        });
    }
}
