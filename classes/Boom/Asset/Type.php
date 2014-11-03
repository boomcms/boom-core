<?php

namespace Boom\Asset;

use \DB as DB;

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

    /**
	 * Returns an array of the asset types which exist in the database.
	 *
	 * @return array
	 */
    public static function whichExist()
    {
        $typesAsNumbers = DB::select('type')
            ->distinct(true)
            ->from('assets')
            ->execute()
            ->as_array();

        $typesAsStrings = [];

        foreach ($typesAsNumbers as $type) {
            $type = static::numericTypeToClass($type['type']);

            if ($type) {
                $typesAsStrings[] = $type;
            }
        }

        return $typesAsStrings;
    }
}
