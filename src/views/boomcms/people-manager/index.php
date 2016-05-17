<?= view('boomcms::header', ['title' => 'People']) ?>

<div id="b-topbar" class="b-toolbar">
    <?= $menu() ?>

    <?= $menuButton() ?>
    <?= $button('plus', trans('New person'), ['id' => 'b-people-create']) ?>

    <a href="#" id="b-people-all"> <?= trans('All people') ?></a>
</div>

<main id="b-container">
    <div id="b-people-manager">
        <h1><?= trans('boomcms::people-manager.heading') ?></h1>

        <div id="b-groups">
            <div id="b-groups-header">
                <h2>
                    <?= trans('Groups') ?>
                </h2>
            </div>

            <ul id="b-groups-list"></ul>

            <form id="b-groups-new">
                <input type="text" placeholder="New group" required name="name" />

                <button>
                    <span class="fa fa-plus-circle"></span>
                </button>
            </form>
        </div>

        <div id='b-people-content'></div>
    </div>
</main>

<script type="text/template" id="b-people-table">
    <table class="b-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email address</th>
                <th>Groups</th>
                <th>Last login</th>
                <th></th>
            </tr>
        </thead>

        <tbody></tbody>
    </table>
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

<script type="text/template" id="b-person-view">
    <?= view('boomcms::people-manager.person') ?>
</script>

<script type="text/template" id="b-person-create-form">
	<section>
		<h2><?= trans('boomcms::people-manager.create-heading') ?></h2>

	    <form method="post" action="/boomcms/people/add" class="new-person">
			<label>
				Name
				<input type="text" required name="name" />
			</label>

			<label>
				Email
				<input type="email" required id="create-email" name="email" />
			</label>

			<label class="groups">
				Groups

				<?= view('boomcms::people-manager.group-select') ?>
			</label>

			<?= $button('save', 'save', ['class' => 'b-button-withtext dark']) ?>
		</form>
	</section>
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
