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

		<?php /*if (Gate::allows('editSuperuser', $person)): ?>
            <label for='person-superuser'>
                <?= trans('boomcms::people-manager.superuser') ?>

                <select name="superuser" id="person-superuser">
                    <option value="">No</option>
                    <option value="1">Yes</option>
                </select>
            </label>
        <?php endif*/ ?>

		<div>
			<?= $button('save', 'save', ['id' => 'b-person-save', 'class' => 'b-people-save']) ?>
			<?= $button('trash-o', 'delete', ['id' => 'b-person-delete']) ?>
		</div>
>>>>>>> 35b57695239483db2a9dd86094586fa3fda45c39
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

        <select class='b-person-sites' multiple>

        </select>
    </section>
<?php endif ?>
