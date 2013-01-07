<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Asset helper functions.
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
	 * Supported asset types.
	 * @access	public
	 * @var	array
	 */
	public static $allowed_types = array('image/jpeg', 'image/gif');

	public static $allowed_extensions = array('jpeg', 'gif', 'jpg', 'png');

	/**
	 * Converts a numeric asset type ID into a human readable type.
	 *
	 * @param	integer	$type_id	Numeric asset type.
	 * @return 	string
	 */
	public static function type($type)
	{
		switch ($type)
		{
			case Boom_Asset::IMAGE:
				return 'image';

			case Boom_Asset::VIDEO:
				return 'video';

			case Boom_Asset::PDF:
				return 'pdf';

			case Boom_Asset::TIFF:
				return 'tiff';

			case Boom_Asset::WORD:
				return "MS Word";

			case Boom_Asset::BOTR:
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
	 * @return 	bool	True if the mimetype is supported by the Boom, FALSE if not.
	 */
	public static function is_supported($mimetype)
	{
		return in_array($mimetype, Boom_Asset::$allowed_types);
	}

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
			return Boom_Asset::IMAGE;
		}

		if (strpos($mime, 'video/') === 0)
		{
			return Boom_Asset::VIDEO;
		}

		// MS Word
		if (strpos($mime, 'application/msword') === 0 OR strpos($mime, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') === 0)
		{
			return Boom_Asset::WORD;
		}

		/**
		 * Although PDF files should be application/pdf it's possible that they may also be application/x-pdf
		 * To prevent problems just check for 'pdf' in the mimetype.
		 */
		if (strpos($mime, 'pdf') !== FALSE)
		{
			return Boom_Asset::PDF;
		}

		return 0;
	}
}