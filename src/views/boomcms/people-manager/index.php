<?= view('boomcms::header', ['title' => trans('boomcms::people-manager.title')]) ?>

<div id="b-topbar" class="b-toolbar">
    <?= $menu() ?>
    <?= $menuButton() ?>
</div>

<main id="b-container">
    <div id="b-people-manager">
        <h1><a href="#"><?= trans('boomcms::people-manager.heading') ?></a></h1>

        <div id="b-groups">
            <div id="b-groups-header">
                <h2>
                    <?= trans('boomcms::people-manager.groups-heading') ?>
                </h2>
            </div>

            <ul id="b-groups-list"></ul>

            <form id="b-groups-new">
                <input type="text" placeholder="<?= trans('boomcms::people-manager.new-group') ?>" required name="name" />

                <button type="submit">
                    <span class="fa fa-plus-circle"></span>
                </button>
            </form>
        </div>

        <div id='b-people-content'></div>
    </div>
</main>

<script type="text/template" id="b-people-table">
    <h2><%= group ? group.getName() : "<?= trans('boomcms::people-manager.all-people') ?>" %></h2>

    <form method="post" action="#" id="b-people-create">
        <table class="b-table">
            <thead>
                <tr>
                    <th><?= trans('boomcms::people-manager.person.name') ?></th>
                    <th><?= trans('boomcms::people-manager.person.email') ?></th>
                    <th><?= trans('boomcms::people-manager.person.groups') ?></th>
                    <th><?= trans('boomcms::people-manager.person.last-login') ?></th>
                    <th></th>
                </tr>
            </thead>

            <tbody></tbody>

            <tfoot>
                <tr>
                    <td>
                        <label>
                            <span><?= trans('boomcms::people-manager.person.name') ?></span>
                            <input type="text" required name="name" placeholder="<?= trans('boomcms::people-manager.person.name') ?>" />
                        </label>
                    </td>

                    <td>
                        <label>
                            <span><?= trans('boomcms::people-manager.person.email') ?></span>
                            <input type="email" required name="email" placeholder="<?= trans('boomcms::people-manager.person.email') ?>" />
                        </label>
                    </td>

                    <td colspan="2">
                        <label class="groups">
                            <span><?= trans('boomcms::people-manager.person.groups') ?></span>
                            <?= view('boomcms::people-manager.group-select') ?>
                        </label>
                    </td>

                    <td>
                        <button type="submit">
                            <span class="fa fa-plus-circle"></span>
                        </button>
                    </td>
                </tr>
            </tfoot>
        </table>
    </form>
</script>

<script type="text/template" id="b-group-list-item">
    <div>
        <a class='b-groups-item' href='#group/<%= id %>'><%= name %></a>

        <a href='#group/<%= id %>/edit' class="fa fa-edit edit"></a>
        <a href='#' class="fa fa-trash-o delete"></a>
    </div>
</script>

<script type="text/template" id="b-group-edit">
    <?= view('boomcms::people-manager.group') ?>
</script>

<script type="text/template" id="b-people-table-item">
    <td>
        <a href="#person/<%= person.getId() %>"><%= person.getName() %></a>
    </td>

    <td>
        <%= person.getEmail() %>
    </td>

    <td>
        <ul class='groups'>
            <% for (var i in groups) { %>
                 <li><a href="#group/<%= groups[i].getId() %>"><%= groups[i].getName() %></a></li>                
            <% } %>
        </ul>
    </td>

    <td>
        <time datetime='<%= person.getLastLogin() %>'></time>
    </td>

    <td>
         <a href='#' class="fa fa-trash-o delete"></a>
    </td>
</script>

<script type="text/template" id="b-person-view-template">
    <?= view('boomcms::people-manager.person') ?>
</script>

<script type="text/javascript" src="/vendor/boomcms/boom-core/js/people-manager.js"></script>

<script type="text/javascript">
    window.addEventListener('load', function() {
        new BoomCMS.PeopleManager({
            groups: <?= Group::findBySite(Router::getActiveSite())->toJson() ?>,
            people: <?= Person::findBySite(Router::getActiveSite())->toJson() ?>,
            sites: <?= Site::findAll()->toJson() ?>
        });
    });
</script>

<?= view('boomcms::footer') ?>
