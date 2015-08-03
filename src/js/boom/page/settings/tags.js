$.widget('boom.pageSettingsTags', {
	baseUrl: '/cms/page/tags/',

	addRelatedPage: function() {
		var page = this.page,
			$relatedPages = this.dialog.find('#pages ul'),
			$el = this.element;

		new boomLinkPicker(new boomLink(), {
				external: false
			})
			.done(function(link) {
				page.addRelatedPage(link.getPageId())
					.done(function() {
						var $li = $('<li></li>')
								.append('<span class="title">' + link.getTitle() + '</span>')
								.append('<span class="uri">' + link.getUrl() + '</span>')
								.append('<a href="#" class="fa fa-trash-o"><span>Remove</span></a>');

						$relatedPages.append($li);
						$el.find('#pages .current').show();
					});
			});
	},

	addTag: function(group, tag) {
		var tagEditor = this;

		$.post(this.getUrl('add'), {
			group : group,
			tag : tag.name
		});
	},

	addTagGroup: function(name) {
		if (name) {
			var $newGroup = $('<li><p>' + name + '</p><ul class="b-tags-list" data-group="' + name + '"><li class="b-tag"></li></ul></li>');

			$newGroup.find('.b-tag').html(this.dialog.find('.b-tags-add').first().clone());
			$newGroup.insertBefore(this.dialog.find('.b-tags-grouped').children().last());
			$newGroup.find('.b-tags-add input[type=text]').focus();

			this.initTagList($newGroup.find('.b-tags-list'));
		}
	},

	bind: function() {
		var tagEditor = this,
			page = this.page;

		this.element
			.on('submit', '.b-tags-newgroup form', function(e) {
				e.preventDefault();

				var $input = $(this).find('input[type=text]');

				tagEditor.addTagGroup($input.val());
				$input.val('');
			})
			.on('click', '#b-tags-addpage', function() {
				tagEditor.addRelatedPage();
			})
			.on('click', '#pages li a', function() {
				tagEditor.removeRelatedPage($(this));
			});

		this.initTagList(this.element.find('.b-tags-list'));
	},

	_create: function() {
		this.page = this.options.page;
	},

	getUrl: function(action) {
		return this.baseUrl + action + '/' + this.page.id;
	},

	initTagList: function($list) {
		$list.pageTagSearch({
			addTag : function(e, data) {
				page.addTag(data.group, data.tag);
			},
			removeTag : function(e, tagId) {
				page.removeTag(tagId);
			}
		});
	},

	removeRelatedPage: function($a) {
		var $el = this.element,
			$relatedPages = $el.find('#pages ul'),
			$current = $el.find('#pages .current');

		this.page.removeRelatedPage($a.attr('data-page-id'))
			.done(function() {
				$a.parent().remove();

				$relatedPages.find('li').length ? $current.show() : $current.hide();
			});
	},

	removeTag: function($a) {
		$.post(this.getUrl('remove'), {
			tag : $a.attr('data-tag_id')
		})
		.done(function() {
			$a.parent().remove();
		});
	},

	updateTagList: function() {
		var tagEditor = this;

		$.get(this.getUrl('list'));
	}
});