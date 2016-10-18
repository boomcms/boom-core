<div id="b-assets-view-albums"></div>

<script type="text/template" id="b-assets-album-template">
    <a class="album-thumb loading" data-asset="<%= asset.getId() %>" tabindex="0">
        <img class="loading" alt="">

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
                <%= BoomCMS.assetTypes[asset.getType()] %><br />

                <% if (asset.isImage()) { %>
                    <%= asset.getWidth() %> x <%= asset.getHeight() %>
                <% } else { %>
                    <%= asset.getReadableFilesize() %>
                <% } %>
            </p>
        </div>
    </a>

    <a href="#" class="edit" tabindex="0">
        <span class="fa fa-edit"></span>
    </a>
</script>
