<?php

namespace BoomCMS\Core\Models\Chunk;

use BoomCMS\Core\Page\Version;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BaseChunk extends Model
{
	public $guarded = ['id'];
	public $timestamps = false;

	public function scopeLatestEdit($query, Version $upToVersion)
	{
		$query
			->select("{$this->table}.*")
			->addSelect("{$this->table}.slotname as slotname")
			->join('page_versions as v1', "{$this->table}.page_vid", '=', 'v1.id')
			->leftJoin("{$this->table} as c2", function($query) use ($upToVersion)  {
				$query
					->on("{$this->table}.id", '<', 'c2.id')
					->on("{$this->table}.slotname", '=', 'c2.slotname')
					->on('c2.page_vid', '<=', DB::raw("'{$upToVersion->getId()}'"));
			})
			->leftJoin('page_versions as v2', function($query) use ($upToVersion) {
				$query
					->on('c2.page_vid', '=', 'v2.id')
					->on('v1.page_id', '=', 'v2.page_id')
					;
			})
			->whereNull('c2.id');

		return $query;
	}
}