<?php

namespace BoomCMS\Core\URL;

use BoomCMS\Core\Models\Page\URL as Model;
use Illuminate\Support\Facades\URL as URLHelper;

class URL
{
    /**
     *
     * @var array
     */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function create($location, $pageId, $isPrimary = false)
    {
        $model = Model::create([
            'location' => $location,
            'page_id' => $pageId,
            'is_primary' => $isPrimary
        ]);

        return new static($model->toArray());
    }

    public function getLocation()
    {
        return $this->data['location'];
    }

    /**
     * Determine whether this URL matches a given URL.
     *
     * @param string $location
     * @return bool
     */
    public function is($location)
    {
        return $this->getLocation() === $location;
    }

    public function __toString()
    {
        return URLHelper::to($this->getLocation());
    }
}