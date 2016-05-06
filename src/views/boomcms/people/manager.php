<?= view('boomcms::header', ['title' => 'People']) ?>

<div id="b-topbar" class="b-toolbar">
    <?= $menu() ?>

    <?= $menuButton() ?>
    <?= $button('plus', trans('New person'), ['id' => 'b-people-create']) ?>
    <?= $button('trash-o', trans('Delete'), ['id' => 'b-people-multi-delete', 'disabled' => 'disabled']) ?>

    <button id="b-people-all" class="b-button">
        <?= trans('All people') ?>
    </button>
</div>

<main id="b-container">
    <div id="b-people-manager">
        <h1><?= trans('boomcms::people.heading') ?></h1>

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

        <div id='b-people-content'>
            <?= $content ?>
        </div>
    </div>
</main>

<script type="text/template" id="b-group-list-item">
    <div>
        <a class='b-groups-item' href='#'><%= name %></a>

        <a href='#' class="fa fa-edit edit"></a>
        <a href='#' class="fa fa-trash-o delete"></a>
    </div>
</script>

<script type="text/template" id="b-group-edit">
    <?= view('boomcms::groups.edit') ?>
</script>

<script type="text/template" id="b-people-table">
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

        </tbody>
    </table>
</script>

<script type="text/template" id="b-people-table-item">
    <td width="10">
        <input type="checkbox" class="b-people-select" />
    </td>

    <td>
        <a href="<%= id %>"><%= name %></a>
    </td>

    <td>
        <%= email %>
    </td>

    <td>
        <ul class='groups'>

        </ul>
    </td>

    <td>
    </td>    
</script>

<script type="text/template" id="b-person-view">
    <?= view('boomcms::person.view') ?>
</script>

<script type="text/template" id="b-person-create-form">
	<section>
		<h2><?= trans('boomcms::people.create-heading') ?></h2>

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

				<select name="groups[]" multiple>
					<% for (var i in groups) { %>
						<option value="<%= groups[i].id %>"><%= groups[i].get('name') %></option>
					<% } %>

				</select>
			</label>

			<?= $button('save', 'save', ['class' => 'b-button-withtext']) ?>
		</form>
	</section>
</script>

<script type="text/javascript">
    window.addEventListener('load', function() {
        $('body').peopleManager();
    });
</script>

<?= view('boomcms::footer') ?>
