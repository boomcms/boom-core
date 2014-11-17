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
					.attr('data-url', link.getUrl())
					.text(link.getTitle());

				linksetEditor.$links.append($('<li></li>').append($a));
				linksetEditor.dialog.contents.find('.none').hide();
				linksetEditor.addDeleteButtons();
			});
	};

	boomChunkLinksetEditor.prototype.bind = function() {
		var linksetEditor = this;

		if ( ! this.options.title) {
			this.dialog.contents.find('#b-linkset-title, #b-linkset-title-tab').hide();
		}

		this.$links = this.dialog.contents.find('#b-linkset-links ul');

		this.addDeleteButtons();

		this.dialog.contents
			.on('click', '#b-linkset-add', function() {
				linksetEditor.addLink();
			})
			.on('click', '.delete', function() {
				linksetEditor.deleteLink($(this).parent());
			})
			.find('ul')
			.sortable();
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

		this.$links.find('a:not(.delete)').each(function(sequence) {
			var $this = $(this);

			links.push({
				target_page_id : $this.attr('data-page-id'),
				url : $this.attr('data-url'),
				title : $this.text(),
				sequence : sequence
			});
		});

		return links;
	};

	boomChunkLinksetEditor.prototype.open = function() {
		var linksetEditor = this;

		this.dialog = new boomDialog({
			url: '/cms/chunk/linkset/edit/' + this.pageId + '?slotname=' + this.slotname,
			title: 'Edit linkset',
			id: 'b-linkset-editor',
			width: 400,
			onLoad: function() {
				linksetEditor.bind();
			}
		})
		.done(function() {
			linksetEditor.deferred.resolve(linksetEditor.getData());
		});

		return this.deferred;
	};

	return this.open();
};