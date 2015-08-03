<form>
    <h1><?= Lang::get('boom::settings.search.heading') ?></h1>

	<div id="b-pagesettings-search">
		<section id="basic">
            <h2><?= Lang::get('boom::settings.basic') ?></h2>

            <label>
                <p><?= Lang::get('boom::settings.search.description') ?></p>
                <textarea name="description" rows="5"><?= $page->getDescription() ?></textarea>
            </label>

            <label>
                <p><?= Lang::get('boom::settings.search.keywords') ?></p>
                <textarea name="keywords" rows="5"><?= $page->getKeywords() ?></textarea>
            </label>
		</section>

		<?php if ($allowAdvanced): ?>
			<section id="advanced">
                <h2><?= Lang::get('boom::settings.advanced') ?></h2>

                <label>
                    <p><?= Lang::get('boom::settings.search.external') ?></p>
                    <select name="external_indexing" id="external_indexing">
                        <option value="1"<?php if ($page->allowsExternalIndexing()): ?> selected="selected"<?php endif ?>>Yes</option>
                        <option value="0"<?php if ( ! $page->allowsExternalIndexing()): ?> selected="selected"<?php endif ?>>No</option>
                    </select>
                </label>

                <label>
                    <p><?= Lang::get('boom::settings.search.internal') ?></p>

                    <select name="internal_indexing" id="internal_indexing">
                        <option value="1"<?php if ($page->allowsInternalIndexing()): ?> selected="selected"<?php endif ?>>Yes</option>
                        <option value="0"<?php if ( ! $page->allowsInternalIndexing()): ?> selected="selected"<?php endif ?>>No</option>
                    </select>
                </label>
			</section>
		<?php endif ?>
	</div>
</form>
