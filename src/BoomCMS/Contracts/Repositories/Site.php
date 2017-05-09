<?php

namespace BoomCMS\Contracts\Repositories;

use BoomCMS\Contracts\Models\Person as PersonInterface;
use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Database\Models\Site as SiteModel;

interface Site extends Repository
{
    /**
     * @param SiteModel $model
     */
    public function __construct(SiteModel $model);

    /**
     * @param array $attrs
     *
     * @return SiteModel
     */
    public function create(array $attrs);

    public function findAll();

    /**
     * @param string $hostname
     *
     * @return null|SiteInterface
     */
    public function findByHostname($hostname);

    /**
     * @param PersonInterface $person
     *
     * @return array
     */
    public function findByPerson(PersonInterface $person);

    /**
     * @return SiteInterface
     */
    public function findDefault();

    /**
     * Make a site the default.
     *
     * @param SiteInterface $site
     */
    public function makeDefault(SiteInterface $site);
}
