<div id="b-items-view-list">
    <table class="b-table">
        <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Email address</th>
                <th>Groups</th>
                <th>Last login</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($people as $person): ?>
                <tr data-person-id="<?= $person->getId() ?>">
                    <td width="10">
                        <input type="checkbox" class="b-people-select" />
                    </td>

                    <td>
                        <a href="/boomcms/person/<?= $person->getId() ?>"><?= $person->getName() ?></a>
                    </td>

                    <td>
                        <?= $person->getEmail() ?>
                    </td>

                    <td>
                        <ul class='groups'>
                            <?php foreach ($person->getGroups() as $group): ?>
                                <li><a href='/boomcms/people?group=<?= $group->getId() ?>'><?= $group->getName() ?></a></li>
                            <?php endforeach ?>​
                        </ul>
                    </td>

                    <td>
                        <?= $person->hasLoggedIn() ? $person->getLastLogin()->diffForHumans() : 'Never' ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
