<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\URL as URLInterface;
use BoomCMS\Contracts\Repositories\URL as URLRepositoryInterface;
use BoomCMS\Database\Models\URL as Model;
use BoomCMS\Support\Helpers\URL as URLHelper;

class URL implements URLRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $location
     * @param int    $pageId
     * @param bool   $isPrimary
     *
     * @return URLInterface
     */
    public function create($location, $pageId, $isPrimary = false)
    {
        $unique = URLHelper::makeUnique(URLHelper::sanitise($location));

        return $this->model->create([
            'location'   => $unique,
            'page_id'    => $pageId,
            'is_primary' => $isPrimary,
        ]);
    }

    /**
     * @param URLInterface $url
     *
     * @return $this
     */
    public function delete(URLInterface $url)
    {
        $this->model->destroy($url->getId());

        return $this;
    }

    /**
     * @param int $id
     *
     * @return URLInterface
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * @param string $location
     *
     * @return URLInterface
     */
    public function findByLocation($location)
    {
        return $this->model
            ->where('location', '=', URLHelper::sanitise($location))
            ->first();
    }

    /**
     * @param URLInterface $url
     *
     * @return URLInterface
     */
    public function save(URLInterface $url)
    {
        return $url->save();
    }
}
