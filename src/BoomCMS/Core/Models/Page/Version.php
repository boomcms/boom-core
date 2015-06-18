<?php

namespace BoomCMS\Core\Models\Page;

use BoomCMS\Core\Page\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Version extends Model
{
    protected $table = 'page_versions';
    public $guarded = ['id'];
    public $timestamps = false;

    /**
	 * Filters for the versioned person columns
	 * @link http://kohanaframework.org/3.2/guide/orm/filters
	 */
    public function filters()
    {
        return [
            'title' => [
                ['strip_tags'],
                ['html_entity_decode'],
                ['trim'],
            ],
            'keywords' => [
                ['trim'],
            ],
            'description' => [
                ['trim'],
            ],
       ];
    }

    /**
	 * Validation rules
	 *
	 * @return	array
	 */
    public function rules()
    {
        return [
            'page_id'    =>    [
                ['not_empty'],
                ['numeric'],
            ],
            'template_id'    =>    [
                ['not_empty'],
                ['numeric'],
            ],
            'title'    =>    [
                ['not_empty'],
                ['max_length', [':value', 70]]
            ],
        ];
    }
	
	public function scopeLastPublished($query)
	{
		// Get the published version with the most recent embargoed time.
		// Order by ID as well incase there's multiple versions with the same embargoed time.
		return $query
			->where('published', '=', true)
			->where('embargoed_until', '<=', time())
			->orderBy('embargoed_until', 'desc')
			->orderBy('id', 'desc');
	}

    public function scopeLatestAvailable($query)
    {
        $editor = App::make('BoomCMS\Core\Editor\Editor');

        if ($editor->isDisabled()) {
            return $this->scopeLastPublished($query);
        } else {
            // For logged in users get the version with the highest ID.
            return $query->orderBy('id', 'desc');
        }
    }
	
	public function scopeForPage($query, Page $page)
	{
		return $query->where('page_id', '=', $page->getId());
	}
}
