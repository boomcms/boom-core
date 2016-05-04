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

        <a href='#' title="Edit" class="fa fa-edit"></a>
        <a href='#' title="Delete" class="fa fa-trash-o b-group-delete"></a>
    </div>
</script>

<script type="text/javascript">
    window.addEventListener('load', function() {
        $('body').peopleManager();
    });
</script>

<?= view('boomcms::footer') ?>
