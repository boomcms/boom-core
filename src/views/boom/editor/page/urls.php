<div id='b-page-urls' class="b-pagesettings">
    <section>
        <h1>Primary and secondary URLs</h1>

        <p>
            Below is a list of all URLs for the page.
        </p>

        <ul class='b-page-urls-help'>
            <li>The highlighted URL indicates the page's primary URL.</li>
            <li>You may only have one primary URL for the page which cannot be deleted.</li>
            <li>All non-primary URLs will redirect to the primary URL.</li>
            <li>Click on a URL to make it the primary URL for the page.</li>
            <li>There is no 'edit' URL. Instead of editing a URL you should simply add the new URL. This ensures that the page remains accessible from the existing URL.</li>
        </ul>

        <ul id='b-page-urls-list'>
            <?php foreach($urls as $url): ?>
                <li data-url="<?= $url->location ?>" data-id="<?= $url->id ?>" <?php if ( (bool) $url->is_primary ): echo 'class="b-page-urls-primary"'; endif ?>>
                    <label class="primary" for="is_primary_<?= $url->id ?>">/<?= $url->location ?></label>

                    <span class='b-page-urls-primary-indicator'>primary</span>
                    <span title="Remove URL" class="ui-icon ui-icon-remove-small b-urls-remove"></span>

                    <input type="radio" name="is_primary" value="<?= $url->location ?>" id="is_primary_<?= $url->id ?>" class="ui-helper-hidden b-urls-primary" <?php if ($url->is_primary): ?> checked="checked"<?php endif ?>/>
                </li>
            <?php endforeach ?>
        </ul>
    </section>

    <section class='b-page-urls-short'>
        <h1>Short URL</h1>
        <p>This URL is automatically generated for use where the shortest URL possible is desirable such as when sharing on social media.</p>
        <p>When used the short URL will redirect to the page's primary URL</p>
        <p>Short URLs always start with an underscore to avoid conflicting with regular URLs. You should therefore avoid using underscores at the start of regular URLs.</p>

        <p class='short-url'>
            <?= URL::site(\Boom\Page\ShortURL::urlFromPage($page), Request::current()) ?>
        </p>
    </section>
</div>
