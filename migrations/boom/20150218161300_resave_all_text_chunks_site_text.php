<?php

use Boom\TextFilter\Commander as TextFilter;
use Boom\TextFilter\Filter as Filter;

class Migration_Boom_20150218161300 extends Minion_Migration_Base
{

	public function up(Kohana_Database $db)
	{
            $chunks = ORM::factory('Chunk_Text')->find_all();

            foreach ($chunks as $c) {
                if ($c->is_block) {
                    $commander = new TextFilter();
                    $commander
                        ->addFilter(new Filter\UnmungeAssetEmbeds())
                        ->addFilter(new Filter\OEmbed())
                        ->addFilter(new Filter\StorifyEmbed())
                        ->addFilter(new Filter\RemoveLinksToInvisiblePages())
                        ->addFilter(new Filter\UnmungeInternalLinks());

                    $c->site_text = $commander->filterText($c->text);
                    $c->update();
                } else if ($c->slotname !== 'standfirst') {
                    $commander = new TextFilter();
                    $commander->addFilter(new Filter\OEmbed());

                    $c->site_text = $commander->filterText($c->text);
                    $c->update();
                }
            }
        }

	public function down(Kohana_Database $db)
	{
	}
}