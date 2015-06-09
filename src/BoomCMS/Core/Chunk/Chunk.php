<?php

namespace BoomCMS\Core\Chunk;

use BoomCMS\Core\Auth\Auth as Auth;
use BoomCMS\Core\Page\Page as Page;
use BoomCMS\Core\Editor\Editor as Editor;

use Illuminate\Support\Facades\Html;

abstract class Chunk
{
    protected $attributePrefix = 'data-boom-';

    /**
     *
     * @var array
     */
    protected $attrs;

    /**
     *
     * @var string
     */
    protected $defaultTemplate;

    /**
     *
     * @var boolean
     */
    protected $editable = true;

    /**
     *
     * @var Page
     */
    protected $page;

    /**
     * An array of parameters which will be passed to the chunk view
     *
     * @var array
     */
    protected $viewParams = [];

    /**
     * The slotname used to find the chunk.
     * This has to be stored seperately to $this->_chunk so that for default chunks where $this->_chunk isn't loaded we know the slotname where the chunk belongs.
     *
     * @var string
     */
    protected $slotname;

    /**
     *
     * @var string
     */
    protected $template;

    /**
     *
     * @var string
     */
    protected $type;

    protected $viewPrefix = 'site.chunks';

    /**
     * Array of available chunk types.
     *
     * @var array
     */
    public static $types = ['asset', 'text', 'feature', 'linkset', 'slideshow', 'timestamp', 'tag'];

    public function __construct(Page $page, array $attrs, $slotname)
    {
        $this->page = $page;
        $this->attrs = $attrs;
        $this->slotname = $slotname;
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->render();
    }

    /**
     * Displays the chunk when chunk data has been set.
     *
     */
    abstract protected function _show();

    /**
     * Displays default chunk HTML
     *
     * @return View
     */
    abstract protected function _showDefault();

    /**
     * Returns whether the logged in user is allowed to edit the chunk
     *
     * @return boolean
     */
    public function allowedToEdit()
    {
        return $this->editor->isEnabled() &&
            ($this->page->wasCreatedBy($this->auth->getPerson())
                || $this->auth->loggedIn("edit_page_content", $this->page)
            );
    }

    /**
     * Attributes to be added to the chunk HTML. Can be overriden to pass additional info to javascript editor.
     *
     * @return array()
     */
    public function attributes()
    {
        return [];
    }

    /**
	 * This adds the necessary classes to chunk HTML for them to be picked up by the JS editor.
	 * i.e. it makes chunks editable.
	 *
	 * @param string $html HTML to add classes to.
	 * @return string
	 */
    public function addAttributesToHtml($html)
    {
        $html = trim( (string) $html);

        $attributes = [
            $this->attributePrefix . 'chunk' => $this->type,
            $this->attributePrefix . 'slot-name' => $this->slotname,
            $this->attributePrefix . 'slot-template' => $this->template,
            $this->attributePrefix . 'page' => $this->page->getId(),
            $this->attributePrefix . 'chunk-id' => isset($this->attrs['id']) ? $this->attrs['id'] : 0,
        ];

        $attributes = array_merge($attributes, $this->attributes());

        $attributesString = HTML::attributes($attributes);

        return preg_replace("|<(.*?)>|", "<$1 $attributesString>", $html, 1);
    }

    public function defaults(array $values)
    {
        $this->_chunk->values($values);

        return $this;
    }

    /**
     * Sets wether the chunk should be editable.
     *
     * @param bool $value
     */
    public function editable($value)
    {
        $this->editable = $value;

        return $this;
    }

    /**
     * Attempts to get the chunk data from the cache, otherwise calls _execute to generate the cache.
     */
    public function render()
    {
        $this->editable = ($this->editable === true && $this->allowedToEdit());

        $html = $this->html();

        if ($this->editable === true) {
            $html = $this->addAttributesToHtml($html);
        }

        return $html;
    }

    /**
	 * Chunk object factory.
	 * Returns a chunk object of the required type.
	 *
	 * @param	string	$type		Chunk type, e.g. text, feature, etc.
	 * @param	string	$slotname		The name of the slot to retrieve a chunk from.
	 * @param	mixed	$page		The page the chunk belongs to. If not given then the page from the current request will be used.
	 * @param	boolean	$inherit		Whether the chunk should be inherited down the page tree.
	 * @return 	Chunk
	 */
    public static function factory($type, $slotname, $page = null)
    {
        // Set the class name.
        $class = "\Boom\Chunk\\" . ucfirst($type);

        // Set the page that the chunk belongs to.
        // This is used for permissions check, and quite importantly, for finding the chunk.
        if ($page === null) {
            // No page was given so use the page from the current request.
            $page = Request::current()->param('page');
        } elseif ($page === 0) {
            // 0 was given as the page - this signifies a 'global' chunk not assigned to any page.
            $page = new Model_Page();
        }

        // Load the chunk
        $chunk = Chunk::find($type, $slotname, $page->getCurrentVersion());

        return new $class($page, $chunk, $slotname);
    }

    public static function find($type, $slotname, Model_Page_Version $version)
    {
        if (is_array($slotname)) {
            return Chunk::find_multiple($type, $slotname, $version);
        } else {
            return Chunk::find_single($type, $slotname, $version);
        }
    }

    public static function find_single($type, $slotname, Model_Page_Version $version)
    {
        $model = (strpos($type, "Chunk_") === 0) ? ucfirst($type) : "Chunk_" . ucfirst($type);

        $query = ORM::factory($model)
            ->with('target')
            ->where('page_vid', '=', $version->id);

        if (is_array($slotname)) {
            return $query
                ->where('slotname', 'in', $slotname)
                ->find_all();
        } else {
            return $query
                ->where('slotname', '=', $slotname)
                ->find();
        }
    }

    public static function find_multiple($type, $slotname, Model_Page_Version $version)
    {
        // Get the name of the model that we're looking.
        // e.g. if type is text we want a chunk_text model
        $model = (strpos($type, "Chunk_") === 0) ? ucfirst($type) : "Chunk_" . ucfirst($type);

        return ORM::factory($model)
            ->with('target')
            ->where('slotname', 'in', $slotname)
            ->where('page_vid', '=', $version->id)
            ->find_all()
            ->as_array();
    }

    /**
	 * Returns whether the chunk has any content.
	 *
	 * @return	bool
	 */
    abstract public function hasContent();

    /**
	 * Generate the HTML to display the chunk
	 *
	 * @return 	string
	 */
    public function html()
    {
        if ($this->template === null) {
            $this->template = $this->defaultTemplate;
        }

        if ($this->hasContent()) {
            // Display the chunk.
            $return = $this->_show();
        } elseif ($this->editable === true) {
            // Show the defult chunk.
            $return = $this->_showDefault();
        } else {
            // Chunk has no content and the user isn't allowed to add any.
            // Don't display anything.
            return "";
        }

        // If the return data is a View then assign any parameters to it.
        if ($return instanceof View && ! empty($this->viewParams)) {
            foreach ($this->viewParams as $key => $value) {
                $return->$key = $value;
            }
        }

        return (string) $return;
    }

    /**
	 * Getter / setter method for template parameters.
	 */
    public function params($params = null)
    {
        if ($params === null) {
            return $this->viewParams;
        } else {
            $this->viewParams = $params;

            return $this;
        }
    }

    /**
	 * Set the template to display the chunk
	 *
	 * @param	string	$template	The name of a view file.
	 * @return	Chunk
	 */
    public function template($template = null)
    {
        // Set the template filename.
        $this->template = $template;

        return $this;
    }
}
