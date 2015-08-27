<?php

use BoomCMS\Database\Models\Chunk\Text;
use Illuminate\Database\Migrations\Migration;

class UpdateAssetLinks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $chunks = Text::where('text', 'like', '%/asset/%')
            ->get();

        foreach ($chunks as $chunk) {
            $chunk->text = preg_replace('|(/asset/)([a-z]+)/([/0-9]+)|', '$1$3/$2', $chunk->text);
            $chunk->text = preg_replace('|(/asset/)([/0-9]+)/view|', '$1$2', $chunk->text);
            $chunk->save();
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
