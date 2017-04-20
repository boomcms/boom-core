<?php

use BoomCMS\Database\Models\Chunk\Linkset;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class RemoveLinksetLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chunk_linksets', function (Blueprint $table) {
            $table->text('links')->nullable();
        });

        $linksets = Linkset::all();

        foreach ($linksets as $linkset) {
            $links = DB::table('chunk_linkset_links')
                ->where('chunk_linkset_id', $linkset->getId())
                ->get()
                ->toArray();

            foreach ($links as &$link) {
                $link = (array) $link;

                unset($link['id']);
                unset($link['chunk_linkset_id']);
            }

            $linkset->links = $links;
            $linkset->save();
        }

        Schema::drop('chunk_linkset_links');
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
