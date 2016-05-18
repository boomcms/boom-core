<div>
	<div>
		<h2 contenteditable><%= name %></h2>
		<a href="#" class="fa fa-edit"></a>
	</div>

    <div id="b-group-roles">
        <div>
            <div id="b-group-roles-general">
                <h3><?= trans('boomcms::people-manager.group.permissions.cms') ?></h3>

                <p>Edit permissions related to the CMS as a whole rather than particular pages in the site tree.</p>
                <?= view('boomcms::people-manager.roles', [
                    'roles' => BoomCMS\Database\Models\Role::getGeneralRoles(),
                ]) ?>
            </div>

            <div id="b-group-roles-pages">
                <h3><?= trans('boomcms::people-manager.group.permissions.page') ?></h3>

                <p>Select a page from the site tree to set or edit the permissions for that page and it's descendants.</p>
                <ul class="boom-tree"></ul>
                <?= view('boomcms::people-manager.roles', [
                    'roles' => BoomCMS\Database\Models\Role::getPageRoles(),
                ]) ?>
            </div>
        </div>

        <div class="b-group-roles-help">
            <p>
                BoomCMS uses a role based permissions system.
            </p>

            <dl class='summary'>
                <dt>Not set</dt>
                <dd>Not allowed - unless allowed in another group or a parent page</dd>
                <dt>Allow</dt>
                <dd>Allowed</dd>
                <dt>Deny</dt>
                <dd>Not allowed</dd>
            </dl>

            <dl class='full'>
                <dt>Not set</dt>
                <dd>
                    <p>Default setting for any role. Permission is denied unless allowed by another group or a parent page.</p>
                    <p>For page permissions if a value is set for a parent page but not a child page then the child will inherit the permissions from the parent.</p>
                </dd>

                <dt>Allow</dt>
                <dd>
                    <p>Members of the group may perform the role.</p>
                    <p>Unless it's been denied by another group they're a member of.</p>
                </dd>

                <dt>Deny</dt>
                <dd>
                    <p>Members of the group cannot perform the role.</p>
                    <p>DENY takes precedence over ALLOW. Setting DENY for any role ensures a group will not have access to that functionality regardless of membership of other groups.</p>
                    <p>If a user is a member of two groups in which one allows a role while the other denies it you they won't be able to perform the role.</p>
                </dd>
            </dl>
        </div>
    </div>
</div>
