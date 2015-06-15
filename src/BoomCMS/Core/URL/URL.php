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
    private $attrs;

    public function __construct(array $attrs)
    {
        $this->attrs = $attrs;
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

    public function get($key)
    {
        return isset($this->attrs[$key]) ? $this->attrs[$key] : null;
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function getLocation()
    {
        return $this->get('location');
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

    public function isPrimary()
    {
        return $this->get('is_primary') == true;
    }

    public function __toString()
    {
        return URLHelper::to($this->getLocation());
    }
}