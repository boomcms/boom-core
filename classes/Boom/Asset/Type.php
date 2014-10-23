<?php

namespace Boom\Asset;

abstract class Type
{
	const IMAGE = 1;
	const PDF = 2;
	const VIDEO = 3;
	const TIFF = 4;
	const MP3 = 5;
	const MSWORD = 6;

	/**
	 *
	 * @param integer
	 * @return  string
	 */
	public static function numericTypeToClass($type)
	{
		switch ($type) {
			case static::IMAGE:
				return 'Image';

			case static::VIDEO:
				return 'Video';

			case static::PDF:
				return 'PDF';

			case static::TIFF:
				return 'Tiff';

			case static::MSWORD:
				return "MSWord";
		}
	}
}