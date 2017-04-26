<script type="text/template" id="b-assets-all-albums-template">
    <div id='b-assets-all-albums'>
        <h1 class='bigger'><?= trans('boomcms::asset.albums') ?></h1>

        <ul>
            <% for (var i = 0; i < albums.models.length; i++) { %>
                <% var album = albums.models[i] %>

                <li>
                    <a href='#albums/<%= album.getId() %>'>
                        <div>
                            <h3><%= album.getName() %></h3>
                            <p><%= album.getAssetCount() %> asset<% if (album.getAssetCount() !== 1) { %>s<% } %>
                        </div>
                    </a>
                </li>
            <% } %>
        </ul>
    </div>
</script>

<script type='text/template' id='b-assets-view-album-template'>
    <div id='b-assets-view-album'>
        <h1 class='bigger'><%= album.getName() %></h1>
        <p class='description'><%= album.getDescription() %></p>

        <?= view('boomcms::assets.thumbs') ?>
    </div>
</script>

<script type="text/template" id="b-assets-none-template">
    <p id="b-assets-none"><?= trans('boomcms::asset.none') ?></p>
</script>

<script type="text/template" id="b-asset-thumb">
    <a href="#asset/<%= asset.getId() %>" class="b-assets-thumbnail thumb loading<% if (!asset.isPublic()) { %> private<% } %>" data-asset="<%= asset.getId() %>" tabindex="0">
        <img class="loading" alt="">

        <div class="private">
            <i class="fa fa-lock"></i>
        </div>

        <div class="pace progress">
            <div>
                <span><?= trans('boomcms::asset.loading') ?></span>
                <div class="pace-activity"></div>
            </div>
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
                <%= BoomCMS.assetTypes[asset.getType()] %><br>

                <% if (asset.getWidth() && asset.getHeight()) { %>
                    <%= asset.getWidth() %> x <%= asset.getHeight() %><br>
                <% } %>

                <%= asset.getReadableFilesize() %>
            </p>
        </div>
    </a>

    <a href="#" class="edit" tabindex="0">
        <span class="fa fa-edit"></span>
    </a>
</script>