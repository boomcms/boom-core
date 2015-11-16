<?php

namespace BoomCMS\Repositories;

use BoomCMS\Core\URL\URL as URLObject;
use BoomCMS\Database\Models\Page\URL as Model;
use BoomCMS\Support\Helpers\URL as URLHelper;

class URL
{
    public function create($location, $pageId, $isPrimary = false)
    {
        $unique = URLHelper::makeUnique(URLHelper::sanitise($location));

        $model = Model::create([
            'location'   => $unique,
            'page_id'    => $pageId,
            'is_primary' => $isPrimary,
        ]);

        return new URLObject($model->toArray());
    }

    public function delete(URLObject $url)
    {
        Model::destroy($url->getId());
    }

    public function findById($id)
    {
        $model = Model::find($id);

        return $model ? new URLObject($model->toArray()) : new URLObject([]);
    }

    public function findByLocation($location)
    {
        $model = Model::where('location', '=', URLHelper::sanitise($location))->first();

        return $model ? new URLObject($model->toArray()) : new URLObject([]);
    }

    public function save(URL $url)
    {
        if ($url->loaded() && $model = Model::find($url->getId())) {
            $model->update($url->toArray());
        }

        return $url;
    }
}
