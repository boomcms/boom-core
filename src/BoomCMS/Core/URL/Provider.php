<?php

namespace BoomCMS\Core\URL;

use BoomCMS\Core\Models\Page\URL as Model;

class Provider
{
    public function create($location, $pageId, $isPrimary = false)
    {
        $model = Model::create([
            'location' => Helpers::sanitise($location),
            'page_id' => $pageId,
            'is_primary' => $isPrimary
        ]);

        return new URL($model->toArray());
    }

    public function findById($id)
    {
        $model = Model::find($id);

        return $model ? new URL($model->toArray()) : new URL([]);
    }

    public function findByLocation($location)
    {
        $model = Model::where('location', '=', Helpers::sanitise($location))->first();

        return $model ? new URL($model->toArray()) : new URL([]);
    }
}