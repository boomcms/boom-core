<?php

namespace Boom\Model;

use Illuminate\Database\Eloquent\Model;

class Page_URL extends Model
{
    protected $_belongs_to = ['page' => ['foreign_key' => 'page_id']];
    protected $_table_columns = [
        'id'            =>    '',
        'page_id'        =>    '',
        'location'        =>    '',
        'is_primary'    =>    '',
    ];
    protected $table = 'page_urls';

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
	 * Calls [Boom_Model_Page_URL::make_primary()] when a page URL is created which has the is_primary property set to true.
	 * This removes the need to call is_primary() after creating a URL.
	 *
	 * @param \Validation $validation
	 * @return \Boom_Model_Page_URL
	 */
    public function create(\Validation $validation = null)
    {
        parent::create($validation);

        // Ensure that this is the only primary URL for this page.
        $this->is_primary && $this->make_primary();

        return $this;
    }

    public function rules()
    {
        return [
            'page_id' => [
                ['not_empty'],
                ['numeric'],
            ],
            'location' => [
                ['max_length', [':value', 2048]],
                [['\Boom\Page\URL', 'isAvailable'], [':value', $this->id]],
            ],
        ];
    }

    public function filters()
    {
        return [
            'location' => [
                [['\Boom\Page\URL', 'sanitise']]
            ],
        ];
    }

    public function getPage()
    {
        return \Boom\Page\Factory::byId($this->page_id);
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
