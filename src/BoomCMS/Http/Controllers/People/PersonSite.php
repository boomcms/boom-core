<?php

namespace BoomCMS\Http\Controllers\People;

use BoomCMS\Database\Models\Person as PersonModel;
use BoomCMS\Database\Models\Site;

class PersonSite extends PeopleManager
{
    /**
     * Add the user to a site.
     *
     * @param PersonModel $person
     * @param Site        $site
     */
    public function update(PersonModel $person, Site $site)
    {
        $person->addSite($site);
    }

    /**
     * Remove the user from a site.
     *
     * @param PersonModel $person
     * @param Site        $site
     */
    public function destroy(PersonModel $person, Site $site)
    {
        $person->removeSite($site);
    }
}
