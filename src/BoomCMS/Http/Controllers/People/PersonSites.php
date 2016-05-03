<?php

namespace BoomCMS\Http\Controllers\People;

use BoomCMS\Database\Models\Person;
use BoomCMS\Database\Models\Site;

class PersonSites extends PeopleManager
{
    /**
     * Add the user to a site.
     *
     * @param Person $person
     * @param Site   $site
     */
    public function store(Person $person, Site $site)
    {
        $person->addSite($site);
    }

    /**
     * Remove the user from a site.
     *
     * @param Person $person
     * @param Site   $site
     */
    public function destroy(Person $person, Site $site)
    {
        $person->removeSite($site);
    }
}
