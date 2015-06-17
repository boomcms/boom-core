<?php

namespace BoomCMS\Core\Models\Chunk;

use BoomCMS\Core\Page\Version;
use Illuminate\Database\Eloquent\Model;

class BaseChunk extends Model
{
	public function scopeLatestEdit($query, Version $upToVersion)
	{
		$query
			->join('page_versions as v1', 'page_vid', '=', 'v1.id')
			->join('pages', 'v1.page_id', '=', 'pages.id')
			->leftJoin('page_versions as v2', function($query)  {
				$query
					->on('v1.page_id', '=', 'v2.page_id')
					->on('v1.id', '<', 'v2.id');
			})
			->whereNull('v2.id')
			->where('v2.id', '<=', $upToVersion->getId());
			
		return $query;
	}
}