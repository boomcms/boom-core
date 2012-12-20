<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Defines a decorator class for use with assets.
 * Assets can be one of many different types - video, image, application etc.
 * Each type of asset will need different methods for things like retrieval.
 * This class therefore creates an 'interface' of which there are different sub-types which we can use for performing actions on the asset.
 * This is an example of the decorator pattern.
 * @link 	http://en.wikipedia.org/wiki/Decorator_pattern
 *
 * @package	BoomCMS
 * @category	Assets
 *
 */
abstract class Boom_Asset_Core
{
	const IMAGE = 1;

	const PDF = 2;

	const VIDEO = 3;

	const TIFF = 4;

	const MP3 = 5;

	const WORD = 6;

	/**
	 * Asset type for Bits on the Run hosted video.
	 */
	const BOTR = 7;

	/**
	 * @access	protected
	 * @var	object	An instance of the asset_Model class which we are providing an interface to.
	 */
	protected $_asset;

	/**
	 * Supported asset types.
	 * @access	public
	 * @var	array
	 */
	public static $allowed_types = array('image/jpeg', 'image/gif');

	public static $allowed_extensions = array('jpeg', 'gif', 'jpg', 'png');

	public function __construct(Model_Asset $asset)
	{
		$this->_asset = $asset;
	}

	/**
	 * Getter for Sledge_Asset::$_asset
	 *
	 * @return	Model_Asset
	 */
	public function asset()
	{
		return $this->_asset;
	}

	/**
	 * Used to initialise the decorator.
	 * Determines which decoration we need from the asset_type name
	 *
	 * @param	string	$type	The type of asset
	 * @param	mixed	$asset	The asset we're decorating. Can be the object or asset ID.
	 * @return 	Asset
	 */
	public static function factory($asset)
	{
		if ( ! is_object($asset))
		{
			$asset = new Model_Asset($asset);
		}

		switch ($asset->type)
		{
			case Sledge_Asset::IMAGE:
				return new Asset_Image($asset);
				break;

			case Sledge_Asset::VIDEO:
				return new Asset_Video($asset);
				break;

			case Sledge_Asset::MP3:
				return new Asset_MP3($asset);
				break;

			case Sledge_Asset::PDF:
				return new Asset_PDF($asset);
				break;

			case Sledge_Asset::WORD:
				return new Asset_Word($asset);
				break;

			case Sledge_Asset::BOTR:
				return new Asset_Botr($asset);
				break;

			default:
				return new Asset_Default($asset);
		}
	}

	/**
	 * Returns the HTML to embed a particular type of asset
	 * e.g., <img> for an image, <iframe> for video
	 *
	 * This is the default behaviour for assets which don't have their own implementation of this function.
	 * the default is to have an img tag referencing the asset thumbnail.
	 *
	 */
	public function embed()
	{
		return HTML::image('asset/thumb/' . $this->_asset->id);
	}

	/**
	 * Converts a numeric asset type ID into a human readable type.
	 *
	 * @param	integer	$type_id	Numeric asset type.
	 * @return 	string
	 */
	public static function get_type($type_id)
	{
		switch ($type_id)
		{
			case Sledge_Asset::IMAGE:
				return 'image';

			case Sledge_Asset::VIDEO:
				return 'video';

			case Sledge_Asset::PDF:
				return 'pdf';

			case Sledge_Asset::TIFF:
				return 'tiff';

			case Sledge_Asset::WORD:
				return "MS Word";

			case Sledge_Asset::BOTR:
				return "video";

			default:
				return $type_id;
		}
	}

	/**
	 * Determine whether a mimetype is supported.
	 * Used to check whether a file can be uploaded.
	 *
	 * @param	string	$mimetype	The mimetype we're checking.
	 * @return 	bool	True if the mimetype is supported by the Sledge, FALSE if not.
	 */
	public static function is_supported($mimetype)
	{
		return in_array($mimetype, Sledge_Asset::$allowed_types);
	}

	/**
	* Method to preview the asset in a page
	*/
	abstract function preview(Response $response);

	/**
	* Method to show the asset
	*/
	abstract function show(Response $response);

	/**
	* Determine the asset type ID from a mimetype.
	*
	* @param string $mime Mimetype.
	* @return int
	*/
	public static function type_from_mime($mime)
	{
		if (strpos($mime, 'image/') === 0)
		{
			return Sledge_Asset::IMAGE;
		}

		if (strpos($mime, 'video/') === 0)
		{
			return Sledge_Asset::VIDEO;
		}

		// MS Word
		if (strpos($mime, 'application/msword') === 0 OR strpos($mime, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') === 0)
		{
			return Sledge_Asset::WORD;
		}

		/**
		* Although PDF files should be application/pdf it's possible that they may also be application/x-pdf
		* To prevent problems just check for 'pdf' in the mimetype.
		*/
		if (strpos($mime, 'pdf') !== FALSE)
		{
			return Sledge_Asset::PDF;
		}

		return 0;
	}
}