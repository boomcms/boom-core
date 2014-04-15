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
	const MSWORD = 6;

	/**
	 * @var	array
	 */
	public static $allowed_types = array(
		'image/jpeg' => 'jpg',
		'image/gif' => 'gif',
		'image/png' => 'png',
		'image/tiff' => 'tiff',
		'application/msword' => 'doc',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
		'application/pdf' => 'pdf',
		'video/mp4' => 'mp4'
	);

	public static $allowed_extensions = array('jpeg', 'gif', 'jpg', 'png', 'tiff', 'doc', 'docx', 'pdf', 'mp4');

	/**
	 * Directory where asset files are stored.
	 *
	 * @var string
	 */
	public static $path;

	public static function extension_from_mime($mime)
	{
		if (isset(static::$allowed_types[$mime]))
		{
			return static::$allowed_types[$mime];
		}
	}

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
			case static::IMAGE:
				return 'image';

			case static::VIDEO:
				return 'video';

			case static::PDF:
				return 'PDF';

			case static::TIFF:
				return 'tiff';

			case static::MSWORD:
				return "MSWord";

			default:
				return $type;
		}
	}

	/**
	 * Determine whether a mimetype is supported.
	 * Used to check whether a file can be uploaded.
	 *
	 */
	public static function is_supported($mimetype)
	{
		return array_key_exists($mimetype, Boom_Asset::$allowed_types);
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
			return Boom_Asset::MSWORD;
		}

		/**
		 * Although PDF files should be application/pdf it's possible that they may also be application/x-pdf
		 * To prevent problems just check for 'pdf' in the mimetype.
		 */
		if (strpos($mime, 'pdf') !== false)
		{
			return Boom_Asset::PDF;
		}

		return 0;
	}
}