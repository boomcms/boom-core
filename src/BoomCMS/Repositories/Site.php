<?php

namespace BoomCMS\Repositories;

use BoomCMS\Contracts\Models\Person as PersonModelInterface;
use BoomCMS\Contracts\Models\Site as SiteModelInterface;
use BoomCMS\Database\Models\Site as Model;
use BoomCMS\Foundation\Repository;
use Illuminate\Database\Eloquent\Collection;

class Site extends Repository
{
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $attributes
     *
     * @return Model
     */
    public function create(array $attributes)
    {
        return Model::create($attributes);
    }

    public function findAll()
    {
        return $this->model->all();
    }

    /**
     * @param string $hostname
     *
     * @return null|Model
     */
    public function findByHostname($hostname)
    {
        return $this->model->where(Model::ATTR_HOSTNAME, '=', $hostname)->first();
    }

    /**
     * @param PersonModelInterface $person
     *
     * @return Collection
     */
    public function findByPerson(PersonModelInterface $person)
    {
        return $this->model
            ->join('person_site', 'person_site.site_id', '=', 'sites.id')
            ->where('person_site.person_id', '=', $person->getId())
            ->orderBy('name', 'asc')
            ->all();
    }

    /**
     * @return null|SiteModelInterface
     */
    public function findDefault()
    {
        return $this->model->where(Model::ATTR_DEFAULT, '=', true)->first();
    }

    /**
     * @param SiteModelInterface $site
     *
     * @return $this
     */
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
