<?php

namespace BoomCMS\Core\Models\Page;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Version extends Model
{
    protected $table = 'page_versions';

    /**
	 * Adds a chunk to the page version.
	 *
	 * This should only be called when the page version has been saved and therefore has a version ID.
	 *
	 * This function assumes that the specified chunk doesn't already exist for the page version.
	 * I can't think of a situation where we'd ever be updating a chunk which has already been added to a page version.
	 * If we want to update a chunk on a page then we would create a new version and add the chunk to the latest version.
	 * Checking whether a chunk exists and then updating it if necessary would therefore add extra DB queries with little benefit.
	 *
	 * **Examples**
	 *
	 * Add a text chunk to a version:
	 *
	 *		$version->add_chunk('text', 'standfirst', array('text' => 'Some text'));
	 *		$version->add_chunk('text', 'standfirst', array('text' => 'Some text', 'title' => 'A text chunk with a title'));
	 *
	 * Add a feature chunk to a version:
	 *
	 *		$version->add_chunk('feature', 'feature_box_1', array('target_page_id' => 1));
	 *
	 * @param	string	$type	The type of chunk to add, e.g. text, feature, etc.
	 * @param	string	$slotname	The slotname of the chunk
	 * @param	array	$data	Array of values to assign to the new chunk.
	 * @return	Model	Returns the model object for the created chunk
	 * @throws	Exception	An exception is thrown when this function is called on a page version which hasn't been saved.
	 *
	 */
    public function add_chunk($type, $slotname, array $data)
    {
        if ( ! ($this->_saved || $this->_loaded)) {
            throw new Exception('You must call Model_Page_Version::save() before calling Model_Page_Version::add_chunk()');
        }

        $data['slotname'] = $slotname;
        $data['page_vid'] = $this->id;

        $chunk = ORM::factory('Chunk_' . ucfirst($type))
            ->values($data)
            ->create();

        return $chunk;
    }

    /**
	 * Copies the chunks from another page version to this version.
	 *
	 * @param Model_Page_Version $from_version
	 * @param array $exclude An array of slotnames which shouldn't be copied from the other version.
	 * @return Model_Page_Version
	 */
    public function copy_chunks(Model_Page_Version $from_version, array $exclude = null)
    {
        $copier = new \Boom\Page\ChunkCopier($from_version, $this, $exclude);
        $copier->copyAll();

        return $this;
    }

    /**
	 * Embargoes the page version until the specified time.
	 *
	 * @param int	$time	Unix timestamp
	 * @return Model_Page_Version
	 */
    public function embargo($time)
    {
        // Set any previous embargoed versions to unpublished to ensure that they won't be used.
        DB::update('page_versions')
            ->set([
                'published'    =>    false,
            ])
            ->where('embargoed_until', '>', $_SERVER['REQUEST_TIME'])
            ->where('page_id', '=', $this->page_id)
            ->where('id', '!=', $this->id)
            ->execute($this->_db);

        // Updated the embargo time of the new version.
        $this
            ->set('published', true)
            ->set('embargoed_until', $time)
            ->save();

        return $this;
    }

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

    public function scopeLatestPublished($query)
    {
        $editor = App::make('BoomCMS\Core\Editor\Editor');

        if ($editor->isDisabled()) {
            // For site users get the published version with the embargoed time that's most recent to the current time.
            // Order by ID as well incase there's multiple versions with the same embargoed time.
            $query
                ->where('published', '=', true)
                ->where('embargoed_until', '<=', time())
                ->orderBy('embargoed_until', 'desc')
                ->orderBy('id', 'desc');
        } else {
            // For logged in users get the version with the highest ID.
            $query->orderBy('id', 'desc');
        }

        return $query;
    }
}
