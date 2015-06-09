<?php

namespace BoomCMS\Core\Models\Page;

use Illuminate\Database\Eloquent\Model;

class URL extends Model
{
    protected $table = 'page_urls';
    public $guarded = ['id'];
    public $timestamps = false;

    /**
	 * Convert a Model_Page_URL object to a string
	 * Returns the location property for the current model
	 *
	 * @uses URL::site()
	 * @return string
	 */
    public function __toString()
    {
        return \URL::site($this->location, \Request::$current);
    }

    /**
	 * Function to be called when making a link the primary link for a page.
	 * Ensures that this will be the only primary link for a page.
	 *
	 * This function will be called when making an existing link the primary link for a page
	 * Or when the page title is changed and a new link is created which will be made the primary link.
	 *
	 * @return	Model_Page_URL
	 */
    public function make_primary()
    {
        // Ensure that this is the only primary link for the page.
        DB::update($this->_table_name)
            ->set(['is_primary' => false])
            ->where('page_id', '=', $this->page_id)
            ->where('id', '!=', $this->id)
            ->where('is_primary', '=', true)
            ->execute($this->_db);

        // Set the is_primary property for this URL to true.
        $this
            ->set('is_primary', true)
            ->update();

        // Update the primary uri for the page in the pages table.
        DB::update('pages')
            ->set(['primary_uri' => $this->location])
            ->where('id', '=', $this->page_id)
            ->execute($this->_db);

        return $this;
    }
}
