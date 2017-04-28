<div class="b-assets-view">
    <div class="b-settings">
        <div class="b-settings-menu">
            <ul>
                <li class="b-settings-close">
                    <a href="#">
                        <span class="fa fa-close"></span>
                        <?= trans('boomcms::asset.close') ?>
                    </a>
                </li>

                <li>
                    <a href="#b-assets-view-info"<% if (!section || section === 'info') { %> class="selected"<% } %> data-section="info">
                        <span class="fa fa-info"></span>
                        <?= trans('boomcms::asset.info') ?>
                    </a>
                </li>

                <li>
                    <a href="#b-assets-view-attributes"<% if (section === 'attributes') { %> class="selected"<% } %> data-section='attributes'>
                        <span class="fa fa-cogs"></span>
                        <?= trans('boomcms::asset.attributes') ?>
                    </a>
                </li>

                <% if (asset.isDocument()) { %>
                    <li>
                        <a href="#b-asset-preview"<% if (section === 'preview') { %> class="selected"<% } %> data-section='preview'>
                            <span class="fa fa-eye"></span>
                            <?= trans('boomcms::asset.preview.heading') ?>
                        </a>
                    </li>
                <% } %>

                <% if (!asset.isImage()) { %>
                    <li>
                        <a href="#b-assets-view-thumbnail"<% if (section === 'thumbnail') { %> class="selected"<% } %> data-section='thumbnail'>
                            <span class="fa fa-image"></span>
                            <?= trans('boomcms::asset.thumbnail') ?>
                        </a>
                    </li>
                <% } %>

                <li>
                    <a href="#b-asset-albums"<% if (section === 'albums') { %> class="selected"<% } %> data-section='albums'>
                        <span class="fa fa-book"></span>
                        <?= trans('boomcms::asset.albums') ?>
                    </a>
                </li>

                <% if (asset.hasMetadata()) { %>
                    <li>
                        <a href="#b-asset-metadata"<% if (section === 'metadata') { %> class="selected"<% } %> data-section='metadata'>
                        <span class="fa fa-asterisk"></span>
                            <?= trans('boomcms::asset.metadata') ?>
                        </a>
                    </li>
                <% } %>

                <% if (asset.hasPreviousVersions()) { %>
                    <li>
                        <a href="#b-assets-view-files"<% if (section === 'history') { %> class="selected"<% } %> data-section='history'>
                        <span class="fa fa-history"></span>
                            <?= trans('boomcms::asset.history') ?>
                        </a>
                    </li>
                <% } %>

                <li>
                    <a href="#b-asset-replace"<% if (section === 'replace') { %> class="selected"<% } %> data-section="replace">
                        <span class="fa fa-upload"></span>
                        <?= trans('boomcms::asset.replace.heading') ?>
                    </a>
                </li>

                <li class='group'>
                    <a href="<%= asset.getUrl('download') %>">
                        <span class="fa fa-download"></span>
                        <?= trans('boomcms::asset.download') ?>
                    </a>
                </li>

                <li class="b-setting-delete">
                    <a href="#b-assets-delete"<% if (section === 'delete') { %> class="selected"<% } %> data-section='delete'>
                        <span class="fa fa-trash-o"></span>
                        <?= trans('boomcms::asset.delete.heading') ?>
                    </a>
                </li>
            </ul>

            <a href="#" class="toggle">
                <span class="fa fa-caret-right"></span>
                <span class="fa fa-caret-left"></span>
                <span class="text">Toggle menu</span>
            </a>
        </div>

        <div class="b-settings-content">
            <div id="b-assets-view-attributes"<% if (section === 'attributes') { %> class="selected"<% } %>>
                <h1><?= trans('boomcms::asset.attributes') ?></h1>

                <form>
                    <label>
                        <?= trans('boomcms::asset.title') ?>
                        <input type="text" id="title" name="title" value="<%= asset.getTitle() %>" />
                    </label>

                    <label>
                        <?= trans('boomcms::asset.description') ?>
                        <textarea id="description" name="description"><%= asset.getDescription() %></textarea>
                    </label>

                    <label>
                        <?= trans('boomcms::asset.credits') ?>
                        <textarea id="credits" name="credits"><%= asset.getCredits() %></textarea>
                    </label>

                    <label>
                        <?= trans('boomcms::asset.published-at') ?>
                        <input type="text" name="published_at" class="boom-datepicker" data-timestamp="<%= new Date(asset.getPublishedAt()).getTime() / 1000 %>" />
                    </label>

                    <label>
                        <?= trans('boomcms::asset.public.title') ?>

                        <select name="public">
                            <?php foreach ([1, 0] as $value): ?>
                                <option value="<?= $value ?>"
                                    <% if (asset.isPublic() == <?= $value ?>) { %> selected<% } %>
                                >
                                    <?= trans("boomcms::asset.public.$value") ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </label>
                </form>

                <?= $button('save', 'save-changes', ['class' => 'b-assets-save b-button-withtext']) ?>
            </div>

            <% if (!asset.isImage()) { %>
                <div id="b-assets-view-thumbnail"<% if (section === 'thumbnail') { %> class="selected"<% } %>>
                    <h1><?= trans('boomcms::asset.thumbnail') ?></h1>
                    <img src="<%= asset.getUrl('thumb', 500) %>?<%= Date.now() %>" alt="<?= trans('boomcms::asset.thumbnail.heading') ?>">

                    <?= $button('image', 'change-thumbnail', [
                        'id'    => 'b-assets-thumbnail-change',
                        'class' => 'b-button-withtext',
                    ]) ?>
                </div>
            <% } %>

            <div id="b-assets-view-info"<% if (!section || section === 'info') { %> class="selected"<% } %>>
                <h1><%= asset.getTitle() %></h1>

                <% if (asset.isVideo()) { %>
                    <video
                        src="<%= asset.getUrl() + '?' + asset.getEditedAt() %>"
                        controls
                        poster="<%= asset.getUrl('thumb') + '?' +asset.getEditedAt() %>"
                    ></video>
                <% } else if (asset.isAudio()) { %>
                    <audio
                        src="<%= asset.getUrl() + '?' + asset.getEditedAt() %>"
                        controls
                    ></audio>
                <% } else if (asset.isImage()) { %>
                    <div class="b-asset-imageeditor">
                        <img id="b-imageeditor-original" src="<%= asset.getUrl()+ '?' + asset.getEditedAt() %>" />

                        <div class="image-container">
                            <img id="b-imageeditor-image" src="<%= asset.getUrl()+ '?' + asset.getEditedAt() %>" />
                        </div>

                        <div id="b-imageeditor-toolbar">
                            <?= $button('rotate-left', 'rotate-left', ['id' => 'b-imageeditor-rotate-left']) ?>
                            <?= $button('rotate-right', 'rotate-right', ['id' => 'b-imageeditor-rotate-right']) ?>
                            <?= $button('crop', 'crop', ['id' => 'b-imageeditor-crop']) ?>
                            <?= $button('save', 'save-changes', ['class' => 'b-imageeditor-save b-button-withtext']) ?>
                            <?= $button('chevron-circle-left', 'asset-undo', ['id' => 'b-imageeditor-revert', 'class' => 'b-button-withtext']) ?>

                            <div class="crop-tools">
                                <label class="aspect-ratio">
                                    <p><?= trans('boomcms::asset.aspect-ratio') ?></p>

                                    <select>
                                        <option value="">Fluid</option>
                                        <option value="1">1/1</option>
                                        <option value="1.33333">4/3</option>
                                        <option value="0.5">1/2</option>
                                        <option value="0.75">3/4</option>
                                        <option value="1.77778">16/9</option>
                                    </select>
                                </label>

                                <?= $button('check', 'accept-crop', ['id' => 'b-imageeditor-crop-accept', 'class' => 'b-button-withtext']) ?>
                                <?= $button('times', 'cancel', ['id' => 'b-imageeditor-crop-cancel', 'class' => 'b-button-withtext']) ?>
                             </div>
                        </div>
                    </div>
                <% } else { %>
                    <img src="<%= asset.getUrl('thumb', 500) %>">
                <% } %>

                <dl>
                    <dt><?= trans('boomcms::asset.type-heading') ?></dt>
                    <dd><%= BoomCMS.assetTypes[asset.getType()] %></dd>

                    <dt><?= trans('boomcms::asset.filename') ?></dt>
                    <dd><%= asset.getFilename() %></dd>

                    <dt><?= trans('boomcms::asset.extension') ?></dt>
                    <dd><%= asset.getExtension() %></dd>

                    <dt><?= trans('boomcms::asset.filesize') ?></dt>
                    <dd><span id='filesize'><%= asset.getReadableFilesize() %></dd>

                    <% if (asset.getWidth() && asset.getHeight()) { %>
                        <dt><?= trans('boomcms::asset.dimensions') ?></dt>
                        <dd><%= asset.getWidth() %> x <%= asset.getHeight() %></dd>
                    <% } %>

                    <% if (asset.getUploadedBy()) { %>
                        <dt><?= trans('boomcms::asset.uploaded-by') ?></dt>
                        <dd>
                            <%= asset.getUploadedBy().getName() %>&nbsp;
                            <small><%= asset.getUploadedBy().getEmail() %></small>
                        </dd>
                    <% } %>

                    <dt><?= trans('boomcms::asset.uploaded-on') ?></dt>
                    <dd>
                        <time datetime="<%= moment.unix(asset.getUploadedTime()).format() %>"></time>
                    </dd>

                    <% if (!asset.isImage()) { %>
                        <dt><?= trans('boomcms::asset.downloads') ?></dt>
                        <dd><%= asset.getDownloads() %></dd>
                    <% } %>
                </dl>
            </div>

            <div id="b-asset-albums"<% if (section === 'albums') { %> class="selected"<% } %>>
            </div>

            <% if (asset.isDocument()) { %>
                <div id="b-asset-preview"<% if (section === 'preview') { %> class="selected"<% } %>>
                    <h1><?= trans('boomcms::asset.preview.heading') ?></h1>
                    <p><?= trans('boomcms::asset.preview.about') ?></p>

                    <iframe src='//docs.google.com/viewer?embedded=true&url=<%= window.location.protocol + "//" + window.location.hostname + asset.getUrl() %>'></iframe>
                </div>
            <% } %>

            <% if (asset.hasMetadata()) { %>
                <div id="b-asset-metadata"<% if (section === 'metadata') { %> class="selected"<% } %>>
                    <h1><?= trans('boomcms::asset.metadata') ?></h1>

                    <dl>
                        <% var metadata = asset.getMetadata() %>

                        <% for (var key in metadata) { %>
                            <dt><%= key %></dt>
                            <dd><%= metadata[key] %></dd>
                        <% } %>
                    </dl>
                </div>
            <% } %>

            <% if (asset.hasPreviousVersions()) { %>
                <div id="b-assets-view-files"<% if (section === 'history') { %> class="selected"<% } %>>
                    <h1><?= trans('boomcms::asset.history') ?></h1>
                    <p><?= trans('boomcms::asset.history-intro') ?></p>

                    <ul>
                        <% var versions = asset.getVersions() %>
                        <% for (var i = 0; i < versions.length; i++) { %>
                            <li data-version-id="<%= versions[i].getId() %>">
                                <div>
                                    <img src="<%= versions[i].getThumbnail() %>" />
                                </div>

                                <div>
                                    <h3><?= trans('boomcms::asset.edited-by') ?></h3>
                                    <p>
                                        <%= versions[i].getEditedBy().getName() %>&nbsp;
                                        <small><%= versions[i].getEditedBy().getEmail() %></small>
                                    </p>

                                    <h3><?= trans('boomcms::asset.edited-at') ?></h3>
                                    <time datetime="<%= moment.unix(versions[i].getEditedAt()).format() %>"></time>

                                    <% if (i > 0) { %>
                                        <?= $button('undo', 'asset-revert', ['class' => 'b-button-withtext b-assets-revert']) ?>
                                    <% } %>
                                </div>
                            </li>
                        <% } %>
                    </ul>
                </div>
            <% } %>

            <div id="b-asset-replace"<% if (section === 'replace') { %> class="selected"<% } %>>
                <div class="b-assets-upload">
                    <form method="post" enctype="multipart/form-data" class="b-assets-upload-form">
                        <div class="b-assets-upload-container">
                            <div class="b-assets-upload-info">
                                <p>
                                    <?= trans('boomcms::asset.replace.info1') ?>
                                    <label for="b-asset-replace-file">
                                        <?= trans('boomcms::asset.replace.info2') ?>
                                    </label>
                                    <?= trans('boomcms::asset.replace.info3') ?>
                                </p>

                                <p class="message"></p>

                                <div class="b-assets-upload-progress"></div>
                                <?= $button('times', 'cancel', ['class' => 'b-assets-upload-cancel']) ?>
                            </div>

                            <input type="file" name="b-assets-upload-files[]" id="b-asset-replace-file" multiple min="1" max="5" />
                        </div>
                    </form>
                </div>
            </div>

            <div id="b-assets-delete"<% if (section === 'delete') { %> class="selected"<% } %>>
                <h1><?= trans('boomcms::asset.delete.heading') ?></h1>
                <p><?= trans('boomcms::asset.delete.confirm') ?></p>

                <?= $button('trash-o', 'delete-asset', [
                    'class' => 'b-button-withtext b-assets-delete',
                ]) ?>
            </div>
        </div>
    </div>
</div>

<?= view('boomcms::tags.tag') ?>
