function boomChunkLinksetEditor(pageId, slotname, options) {
	this.pageId = pageId;
	this.slotname = slotname;
	this.options = options;
	this.deferred = new $.Deferred();

	boomChunkLinksetEditor.prototype.addDeleteButtons = function() {
		this.$links.find('li').each(function() {
			var $this = $(this);

			if ( ! $this.find('.delete').length) {
				var $delete = $('<a class="delete" href="#"></a>').appendTo($this);
			}
		});
	};

	boomChunkLinksetEditor.prototype.addLink = function() {
		var linksetEditor = this;

		new boomLinkPicker()
			.done(function(link) {
				var $a = $('<a href="#"></a>')
					.attr('data-page-id', link.getPageId())
					.attr('data-title', link.getTitle())
					.attr('data-url', link.getUrl())
					.attr('data-asset', '')
					.text(link.getTitle());

				linksetEditor.$links.append($('<li></li>').append($a));
				linksetEditor.dialog.contents.find('#b-linkset-links .none').hide();
				linksetEditor.addDeleteButtons();
				linksetEditor.editLink($a);
			});
	};

	boomChunkLinksetEditor.prototype.bind = function() {
		var linksetEditor = this;

		if ( ! this.options.title) {
			this.dialog.contents.find('#b-linkset-title, #b-linkset-title-tab').hide();
		}

		if ( ! this.options.linkAssets) {
			this.dialog.contents.find('.b-linkset-asset').hide();
		}

		this.$links = this.dialog.contents.find('#b-linkset-links ul');

		this.addDeleteButtons();

		this.$links
			.on('click', '.b-linkset-link', function() {
				linksetEditor.editLink($(this));
			});

		this.dialog.contents
			.on('click', '#b-linkset-add', function() {
				linksetEditor.addLink();
			})
			.on('click', '.delete', function() {
				linksetEditor.deleteLink($(this).parent());
			})
			.on('keyup change', '#b-linkset-current form input[type=text]', function() {
				var $this = $(this),
					name = $this.attr('name'),
					val = $this.val();

				linksetEditor.currentLink.attr('data-' + name, val);

				if (name === 'title') {
					linksetEditor.currentLink.text(val);
				}
			})
			.on('click', '.b-linkset-target button', function(e) {
				e.preventDefault();

				linksetEditor.editLinkTarget();
			})
			.on('click', '#b-linkset-delete', function() {
				linksetEditor.deferred.resolveWith({});
				linksetEditor.dialog.cancel();
			})
			.on('click', '.b-linkset-asset a', function() {
				linksetEditor.editAsset(new boomAsset(linksetEditor.currentLink.attr('data-asset')));
			})
			.find('ul')
			.sortable();
	};

	boomChunkLinksetEditor.prototype.editAsset = function(currentAsset) {
		var linksetEditor = this;

		new boomAssetPicker(currentAsset)
			.done(function(asset) {
				linksetEditor.currentLink.attr('data-asset', asset.getId());
				linksetEditor.toggleLinkAsset(asset);
			});
	};

	boomChunkLinksetEditor.prototype.editLink = function($a) {
		this.currentLink = $a;

		this.dialog.contents
			.find('#b-linkset-current')
			.find('.default')
			.hide()
			.end()
			.find('form')
			.show()
			.find('.b-linkset-target input[type=text]')
			.val($a.attr('data-url'))
			.end()
			.find('.b-linkset-title input[type=text]')
			.val($a.attr('data-title'))
			.end();

		this.toggleLinkAsset($a.attr('data-asset'));
	};

	boomChunkLinksetEditor.prototype.editLinkTarget = function() {
		var linksetEditor = this,
			link = new boomLink(this.currentLink.attr('data-url'), this.currentLink.attr('data-page-id'));

		new boomLinkPicker(link)
			.done(function(link) {
				linksetEditor.currentLink
					.attr('data-page-id', link.getPageId())
					.attr('data-url', link.getUrl());

				linksetEditor.dialog.contents.find('.b-linkset-target input').val(link.getUrl());
			});
	};

	boomChunkLinksetEditor.prototype.deleteLink = function($li) {
		$li.fadeOut(200, function() {
			$li.remove();
		});
	};

	boomChunkLinksetEditor.prototype.getData = function() {
		return {
			links : this.getLinks(),
			title : this.dialog.contents.find('#b-linkset-title input').val()
		};
	};

	boomChunkLinksetEditor.prototype.getLinks = function() {
		var links = [];

		this.$links.find('a:not(.delete)').each(function() {
			var $this = $(this);

			links.push({
				target_page_id : $this.attr('data-page-id'),
				url : $this.attr('data-url'),
				title : $this.attr('data-title'),
				asset_id : $this.attr('data-asset')
			});
		});

		return links;
	};

	boomChunkLinksetEditor.prototype.open = function() {
		var linksetEditor = this;

		this.dialog = new boomDialog({
			url: '/cms/chunk/' + this.pageId + '/edit?slotname=' + this.slotname + '&type=linkset',
			title: 'Edit linkset',
			id: 'b-linkset-editor',
			width: 900,
			closeButton: false,
			saveButton: true,
			onLoad: function() {
				linksetEditor.bind();
			}
		})
		.done(function() {
			linksetEditor.deferred.resolve(linksetEditor.getData());
		})
		.fail(function() {
			linksetEditor.deferred.reject();
		});

		return this.deferred;
	};

	boomChunkLinksetEditor.prototype.toggleLinkAsset = function(asset) {
		var $linksetAsset = this.dialog.contents.find('.b-linkset-asset');

		if (asset && asset.getId() > 0) {
			$linksetAsset
				.find('.none')
				.hide()
				.end()
				.find('.set')
				.show()
				.find('img')
				.attr('src', asset.getUrl('view', 500));
		} else {
			$linksetAsset
				.find('.none')
				.show()
				.end()
				.find('.set')
				.hide();
		}
	};

	return this.open();
};