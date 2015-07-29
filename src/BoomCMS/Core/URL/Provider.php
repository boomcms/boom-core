<?php

namespace BoomCMS\Core\URL;

use BoomCMS\Support\Helpers\URL as URLHelper;
use BoomCMS\Database\Models\Page\URL as Model;

class Provider
{
    public function create($location, $pageId, $isPrimary = false)
    {
        $unique = URLHelper::makeUnique(Helpers::sanitise($location));

        $model = Model::create([
            'location' => $unique,
            'page_id' => $pageId,
            'is_primary' => $isPrimary
        ]);

        return new URL($model->toArray());
    }

    public function delete(URL $url)
    {
        Model::destroy($url->getId());
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

    public function save(URL $url)
    {
        if ($url->loaded() && $model = Model::find($url->getId())) {
            $model->update($url->toArray());
        }

        return $url;
    }
}
