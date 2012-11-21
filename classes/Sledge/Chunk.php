<?php defined('SYSPATH') OR die('No direct script access.');
/**
* @package Sledge
* @category Chunks
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2012, Hoop Associates
*
*/
abstract class Sledge_Chunk
{
	/**
	* Holds the chunk data retrieved from the database
	* Object type will depend on slottype, e.g. Model_Chunk_Text for text chunk, Model_Chunk_Feature for feature etc.
	*/
	protected $_chunk;

	/**
	* The name of the default template if no template is set.
	*/
	protected $_default_template = NULL;

	/**
	* Whether the chunk should be editable.
	* The initial (default) value of this is set in the constructor depending on whether the current person has the correct permission to edit this chunk.
	* @access	protected
	* @var	boolean
	*/
	protected $_editable;

	/**
	* The page that the chunk belongs to.
	* @access protected
	* @var Model_Page
	*/
	protected $_page;

	/**
	* An array of parameters which will be passed to the chunk template
	* @access protected
	* @var array
	*/
	protected $_params = array();

	/**
	* The name of the template to display
	* @access protected
	* @var string
	*/
	protected $_template;

	/**
	* The type of slot; text, feature, etc.
	* @access protected
	* @var string
	*/
	protected $_type;

	public function __construct(Model_Page_Version $page, $chunk, $editable = TRUE)
	{
		$this->_page = $page;

		$this->_chunk = $chunk;

		$this->_editable = $editable;
	}

	/**
	 * Allows chunks to be displayed without having to call execute() every time.
	 *
	 * e.g.
	 *	<?= Chunk::factory('text', 'standfirst'); ?>
	 * Instead of:
	 *	<?= Chunk::factory('text', 'standfirst')->execute(); ?>
	 *
	 * @return string
	 */
	public function __toString()
	{
		return (string) $this->execute();
	}

	/**
	* Displays the chunk when chunk data has been set.
	*
	* @return View
	*/
	abstract protected function _show();

	/**
	* Displays default chunk HTML
	*
	* @return View
	*/
	abstract protected function _show_default();

	/**
	* Sets wether the chunk should be editable.
	*
	* @param bool $value
	*/
	public function editable($value)
	{
		// Set the value of $_editable.
		$this->_editable = $value;

		return $this;
	}

	/**
	* Attempts to get the chunk data from the cache, otherwise calls _execute to generate the cache.
	*/
	public function execute()
	{
		// If profiling is enabled then record how long it takes to generate this chunk.
		if (Kohana::$profiling === TRUE)
		{
			$benchmark = Profiler::start("Chunks", $this->_chunk->slotname);
		}

		$cache_key = "chunk_data:" . md5($this->_chunk->slotname . $this->_page->id);
		$cache = Cache::instance();

		if (Auth::instance()->logged_in() OR ($html = $cache->get($cache_key)) === NULL)
		{
			// Generate the HTML.
			// Don't allow an error displaying the chunk to bring down the whole page.
			try
			{
				// Get the chunk HTML.
				$html = $this->html();

				// Make the content editable.
				if ($this->_editable === TRUE)
				{
					$html = HTML::chunk_classes($html, $this->_type, $this->_chunk->slotname, $this->target(), $this->_template, $this->_page->id, $this->has_content());
				}
				elseif ( ! Auth::instance()->logged_in() OR Editor::state() == Editor::PREVIEW_PUBLISHED)
				{
					// Save to cache when in site view.
					// Originally the chunk code was designed to be able to generate the cache for the site view when browsing a page in the CMS.
					// But because we can now have things like child page lists in chunk templates this causes problems.
					$cache_contents = ($this->has_content())? $html : "";
					$cache->set($cache_key, (string) $cache_contents);
				}
			}
			catch (Exception $e)
			{
				// Log the error.
				Kohana_Exception::log($e);
				return;
			}
		}

		if (isset($benchmark))
		{
			Profiler::stop($benchmark);
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
	public static function factory($type, $slotname, $page = NULL, $inherit = FALSE)
	{
		// Set the class name.
		$class = "Chunk_" . ucfirst($type);

		// Set the page that the chunk belongs to.
		// This is used for permissions check, and quite importantly, for finding the chunk.
		if ($page === NULL)
		{
			// No page was given so use the page from the current request.
			$page = Request::current()->param('page');
		}
		elseif ($page === 0)
		{
			// 0 was given as the page - this signifies a 'global' chunk not assigned to any page.
			$page = ORM::factory('Page_Version');
		}

		// The chunk is being cascaded down the tree so find the page that the chunk is actually assigned to.
		$page = ($inherit === TRUE)? Chunk::inherit_from($type, $slotname, $page) : $page;

		// Load the chunk
		$chunk = Chunk::find($type, $slotname, $page, $inherit);

		// Should the chunk be editable?
		// This can be changed to calling editable(), for instance if we want to make a chunk read only.
		$editable = (Editor::state() == Editor::EDIT AND Auth::instance()->logged_in("edit_slot_$slotname", $page));

		return new $class($page, $chunk, $editable);
	}

	/**
	 * Finds a chunk model depending on the type, slotname, page version.
	 * This is a helper function which can be called statically anywhere we need to find a chunk.
	 *
	 * @param	string		$type		Type of chunk, e.g. text, feature.
	 * @param	string		$slotname	The name of the slot that the chunk belongs to.
	 * @param	Model_Page_Version	$page_versions		The page version that the chunk belongs to.
	 * @return 	Chunk
	 */
	public static function find($type, $slotname, Model_Page_Version $page)
	{
		// Get the name of the model that we're looking.
		// e.g. if type is text we want a chunk_text model
		$model = (strpos($type, "Chunk_") === 0)? ucfirst($type) : "Chunk_" . ucfirst($type);

		$version_id = $page->id;
		$version_id = (int) $version_id;

		// Try and get it from the cache
		$cache_key = $type . "_" . $slotname . "_" . $version_id;
		$cache = Cache::instance();

		if ($chunk = $cache->get($cache_key))
		{
			return $chunk;
		}

		// Awww, query the database :(
		$chunk = ORM::factory($model)
			->join('page_chunks')
			->on('page_chunks.chunk_id', '=', 'id')
			->where('slotname', '=', $slotname)
			->where('page_chunks.page_vid', '=', $version_id)
			->find();

		// Cache the result
		$cache->set($cache_key, $chunk);

		return $chunk;
	}

	/**
	* Returns whether the chunk has any content.
	*
	* @return	bool
	*/
	abstract public function has_content();

	/**
	 * Generate the HTML to display the chunk
	 *
	 * @return 	string
	 */
	public function html()
	{
		if ($this->_template === NULL)
		{
			$this->_template = $this->_default_template;
		}

		if ($this->has_content())
		{
			// Display the chunk.
			$return = $this->_show();
		}
		elseif ($this->_editable === TRUE)
		{
			// Show the defult chunk.
			$return = $this->_show_default();
		}
		else
		{
			// Chunk has no content and the user isn't allowed to add any.
			// Don't display anything.
			return "";
		}

		// If the return data is a View then assign any parameters to it.
		if ($return instanceof View AND ! empty($this->_params))
		{
			foreach ($this->_params as $key => $value)
			{
				$return->$key = $value;
			}
		}

		return (string) $return;
	}

	/**
	 * Determine which is the nearest page in the tree with a given slot.
	 * Used when inheriting a chunk to determine which page a chunk should be inherited from.
	 *
	 * @param	string		$type		Slottype.
	 * @param	string		$slotname	Name of the slot.
	 * @param	Model_Page	$page		The page the chunk is appearing in, i.e. where the in the tree the chunk should be inherited to.
	 * @return Model_Page
	 */
	public static function inherit_from($type, $slotname, Model_Page_Version $page)
	{
		// Get the name of the model that we're looking.
		// e.g. if type is text we want a chunk_text model
		$type = strtolower($type);
		$table_name = Inflector::plural((strpos($type, "chunk_") === 0)? $type : "chunk_$type");

		$cache_key = 'chunk_inerited_from:' . $type . '_' . $slotname . '_' . $page->id;

		if ( ! $page_id = Cache::instance()->get($cache_key))
		{
			// Awww, query the database :(
			$page_id = DB::select('page_versions.page_id')
				->from($table_name)
				->join('page_chunks')
				->on('page_chunks.chunk_id', '=', "$table_name.id")
				->where('slotname', '=', $slotname)
				->join('page_versions', 'inner')
				->on('page_chunks.page_vid', '=', 'page_versions.id')
				->join('page_mptt', 'inner')
				->on('page_mptt.id', '=', 'page_versions.page_id')
				->where('page_mptt.scope', '=', $page->mptt->scope)
				->where('page_mptt.lft', '<=', $page->mptt->lft)
				->where('page_mptt.rgt', '>=', $page->mptt->rgt)
				->order_by('page_mptt.lft', 'desc')
				->limit(1)
				->execute();

			$page_id = (isset($page_id[0]))? $page_id[0]['id'] : 0;

			Cache::instance()->set($cache_key, $page_id);
		}

		return ORM::factory('Page_Version', array('page_id' => $page_id));
	}

	/**
	* Getter / setter method for template parameters.
	*/
	public function params($params = NULL)
	{
		if ($params === NULL)
		{
			return $this->_params;
		}
		else
		{
			$this->_params = $params;
			return $this;
		}
	}

	/**
	* Returns the target of the slot.
	* Used for adding the cmsclasses.
	*/
	public function target()
	{
		return "0";
	}

	/**
	* Set the template to display the chunk
	*
	* @param string $template The name of a view file.
	* @return Chunk
	*/
	public function template($template = NULL)
	{
		// Set the template filename.
		$this->_template = $template;

		return $this;
	}
}