function boomPageTagEditor(page) {
	this.page = page;
	this.baseUrl = '/cms/page/tags/';

	boomPageTagEditor.prototype.addRelatedPage = function() {
		var page = this.page;

		new boomLinkPicker(new boomLink(), {
				external: false
			})
			.done(function(link) {
				page.addRelatedPage(link.getPageId());
			});
	};

	boomPageTagEditor.prototype.addTag = function(group, tag) {
		var tagEditor = this;

		$.post(this.getUrl('add'), {
			group : group,
			tag : tag.name
		});
	};

	boomPageTagEditor.prototype.addTagGroup = function(name) {
		if (name) {
			var $newGroup = $('<li><p>' + name + '</p><ul class="b-tags-list" data-group="' + name + '"><li class="b-tag"></li></ul></li>');

			$newGroup.find('.b-tag').html(this.dialog.find('.b-tags-add').first().clone());
			$newGroup.insertBefore(this.dialog.find('.b-tags-grouped').children().last());
			$newGroup.find('.b-tags-add input[type=text]').focus();

			this.initTagList($newGroup.find('.b-tags-list'));
		}
	};

	boomPageTagEditor.prototype.bind = function($dialog) {
		var tagEditor = this,
			page = this.page;

		$dialog
			.on('submit', '.b-tags-newgroup form', function(e) {
				e.preventDefault();

				var $input = $(this).find('input[type=text]');

				tagEditor.addTagGroup($input.val());
				$input.val('');
			})
			.on('click', '#b-tags-addpage', function() {
				tagEditor.addRelatedPage();
			});

		this.initTagList($dialog.find('.b-tags-list'));
	};

	boomPageTagEditor.prototype.getUrl = function(action) {
		return this.baseUrl + action + '/' + this.page.id;
	};

	boomPageTagEditor.prototype.initTagList = function($list) {
		$list.pageTagSearch({
			addTag : function(e, data) {
				page.addTag(data.group, data.tag);
			},
			removeTag : function(e, tagId) {
				page.removeTag(tagId);
			}
		});
	};

	boomPageTagEditor.prototype.open = function() {
		var tagEditor = this;

		return new boomDialog({
			url: this.getUrl('list'),
			title: 'Page tags',
			width: 800,
			cancelButton : false,
			open: function() {
				tagEditor.dialog = $(this);
				tagEditor.bind(tagEditor.dialog);
			}
		});
	};

	boomPageTagEditor.prototype.removeTag = function($a) {
		$.post(this.getUrl('remove'), {
			tag : $a.attr('data-tag_id')
		})
		.done(function() {
			$a.parent().remove();
		});
	};

	boomPageTagEditor.prototype.updateTagList = function() {
		var tagEditor = this;

		$.get(this.getUrl('list'))
			.done(function(response) {
//				tagEditor.dialog
			});
	};

	return this.open();
}