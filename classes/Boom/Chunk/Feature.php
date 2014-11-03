<?php

namespace Boom\Chunk;

use \Boom\Page as Page;
use \Boom\Editor\Editor as Editor;
use \Kohana as Kohana;
use \View as View;

class Feature extends \Boom\Chunk
{
    /**
	* holds the page which is being featured.
	* @var Model_Page
	*/
    protected $_target_page;

    protected $_type = 'feature';

    public function __construct(Page\Page $page, $chunk, $editable = true)
    {
        parent::__construct($page, $chunk, $editable);

        $this->_target_page = \Boom\Page\Factory::byId($this->_chunk->target_page_id);
    }

    /**
	* Show a chunk where the target is set.
	*/
    public function _show()
    {
        // If the template doesn't exist then use a default template.
        if ( ! Kohana::find_file("views", $this->viewDirectory."feature/$this->_template")) {
            $this->_template = $this->_default_template;
        }

        // Get the target page.
        $page = $this->target_page();

        // Only show the page feature if the page is visible or the feature box is editable.
        if ( ! Editor::instance()->isDisabled() || $page->isVisible()) {
            return View::factory($this->viewDirectory."feature/$this->_template", [
                'target'    =>    $page,
            ]);
        }
    }

    public function _show_default()
    {
        return View::factory($this->viewDirectory."default/feature/$this->_template");
    }

    public function attributes()
    {
        return [
            $this->attributePrefix.'target' => $this->target(),
        ];
    }

    public function hasContent()
    {
        return $this->_chunk->loaded() && $this->_target_page->loaded();
    }

    public function target()
    {
        return $this->_target_page->getId();
    }

    /**
	 *
	 * @return Model_Page
	 */
    public function target_page()
    {
        return $this->_target_page;
    }
}
