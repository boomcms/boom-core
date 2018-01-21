<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Person as PersonModelInterface;
use BoomCMS\Contracts\Models\Site as SiteModelInterface;
use BoomCMS\Database\Models\Site as Model;
use BoomCMS\Foundation\Repository;
use Illuminate\Support\Collection;

class Site extends Repository
{
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create(array $attributes): Model
    {
        return Model::create($attributes);
    }

    public function findAll()
    {
        return $this->model->all();
    }

    public function findByHostname($hostname)
    {
        return $this->model->where(Model::ATTR_HOSTNAME, '=', $hostname)->first();
    }

    public function findByPerson(PersonModelInterface $person): Collection
    {
        return $this->model
            ->join('person_site', 'person_site.site_id', '=', 'sites.id')
            ->where('person_site.person_id', '=', $person->getId())
            ->orderBy('name', 'asc')
            ->all();
    }

    public function findDefault()
    {
        return $this->model->where(Model::ATTR_DEFAULT, '=', true)->first();
    }

    public function makeDefault(SiteModelInterface $site)
    {
        if (!$site->isDefault()) {
            $this->model
                ->where(Model::ATTR_ID, '!=', $site->getId())
                ->update([
                    Model::ATTR_DEFAULT => false,
                ]);

            $site->setDefault(true);
            $this->save($site);
        }

        return $this;
    }
}
