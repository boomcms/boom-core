<div id="b-person-view">
    <div>
        <h2 class="name" contenteditable><%= person.getName() %></h2>
        <a href="#" class="fa fa-edit"></a>
    </div>

    <p class="email"><%= person.getEmail() %></p>

    <?= $button('trash-o', 'delete', ['id' => 'b-person-delete', 'class' => 'dark']) ?>

    <section>
        <form>
            <label>
                <?= trans('boomcms::people-manager.status') ?>

                <select name="enabled">
                    <option value=""<%= !person.isEnabled() ? ' selected' : ''%>>Disabled</option>
                    <option value="1"<%= person.isEnabled() ? ' selected' : ''%>>Enabled</option>
                </select>
            </label>

            <% if (BoomCMS.user.isSuperuser() && BoomCMS.user.getId() !== person.getId()) { %>
                <label for='person-superuser'>
                    <?= trans('boomcms::people-manager.superuser') ?>

                    <select name="superuser" id="person-superuser">
                        <option value=""<%= !person.isSuperuser() ? ' selected' : ''%>>No</option>
                        <option value="1"<%= person.isSuperuser() ? ' selected' : ''%>>Yes</option>
                    </select>
                </label>
            <% } %>
        </form>
    </section>

    <section>
        <h3><?= trans('boomcms::people-manager.groups-heading') ?></h3>
        <p><?= trans('boomcms::people-manager.groups') ?></p>

        <?= view('boomcms::people-manager.group-select') ?>
    </section>

    <?php if (Gate::allows('manageSites', Router::getActiveSite())): ?>
        <section>
            <h3><?= trans('boomcms::people-manager.sites-heading') ?></h3>
            <p><?= trans('boomcms::people-manager.sites') ?></p>

            <select name="sites" multiple>
                <% for (var i in sites.models) { %>
                    <option value="<%= sites.models[i].getId() %>"<%= person.sites.get(sites.models[i].getId()) ? ' selected' : '' %>><%= sites.models[i].getName() %></option>
                <% } %>
            </select>
        </section>
    <?php endif ?>
</div>
