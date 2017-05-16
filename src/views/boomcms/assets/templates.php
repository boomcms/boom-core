<script type="text/template" id="b-assets-album-list-template">
    <% for (var i = 0; i < albums.models.length; i++) { %>
        <% var album = albums.models[i] %>

        <li data-album='<%= album.getId() %>'>
            <a href='#albums/<%= album.getSlug() %>'<% if (album.getFeatureImage()) { %> data-asset="<%= album.getFeatureImage().getId() %>"<% } %>>
                <div>
                    <h3><%= album.getName() %></h3>
                    <p class='description'><%= album.getDescription() %></p>
                    <p class='count'><span><%= album.getAssetCount() %></span> asset<% if (album.getAssetCount() !== 1) { %>s<% } %></p>
                </div>
            </a>
        </li>
    <% } %>
</script>

<script type='text/template' id='b-assets-view-album-template'>
    <div id='b-assets-view-album'>
        <div class='heading'>
            <h1 class='bigger'><%= album.getName() ? album.getName() : 'Untitled' %></h1>
            <p class='description'><%= album.getDescription() %></p>
        </div>

        <% if (!album.isNew()) { %>
            <div class='controls'>
                <?= view('boomcms::assets.search.sort') ?>

                <label for="b-assets-album-upload-<%= album.getId() %>" tabindex='0'>
                    <span class="fa fa-upload"></span>
                </label>

                <?= $button('trash', 'delete', ['class' => 'delete small dark']) ?>
            </div>

            <?= view('boomcms::assets.thumbs') ?>

            <div class="b-assets-upload">
                <div class='errors'>
                    <h2><?= trans('boomcms::asset.upload.errors') ?></h2>

                    <ul></ul>
                </div>

                <p class='failed'><?= trans('boomcms::asset.upload.failed') ?></p>

                <div class='progress-cancel'>
                    <div class="progress"></div>
                    <?= $button('times', 'cancel', ['class' => 'cancel']) ?>
                </div>

                <input type="file" name="b-assets-upload-files[]" id="b-assets-album-upload-<%= album.getId() %>">
            </div>
        <% } %>
    </div>
</script>

<script type="text/template" id="b-asset-thumb">
    <a href="#asset/<%= asset.getId() %>" class="b-assets-thumbnail thumb loading<% if (!asset.isPublic()) { %> private<% } %>" data-asset="<%= asset.getId() %>" tabindex="0">
        <img class="loading" alt="">

        <div class="private">
            <i class="fa fa-lock"></i>
        </div>

        <div class="failed">
            <div>
                <span class="fa fa-frown-o"></span>
                <p><?= trans('boomcms::asset.failed') ?></p>
            </div>
        </div>

        <div class="b-asset-details">
            <h2><%= asset.getTitle() %></h2>

            <p>
                <span class='type'><%= BoomCMS.assetTypes[asset.getType()] %></span><br>

                <% if (asset.getWidth() && asset.getHeight()) { %>
                    <%= asset.getWidth() %> x <%= asset.getHeight() %><br>
                <% } %>

                <span class='filesize'><%= asset.getReadableFilesize() %></span>
            </p>
        </div>
    </a>

    <a href="#" class="edit" tabindex="0">
        <span class="fa fa-edit"></span>
    </a>
</script>