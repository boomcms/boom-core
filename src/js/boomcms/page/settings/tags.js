$.widget('boom.pageSettingsTags', {
	baseUrl: '/boomcms/page/tags/',

	addTag: function(group, tag, $el) {
		this.page.addTag(group, tag).done(function(tagId) {
			$el.find('a').attr('data-tag', tagId);
		});
	},

	addTagGroup: function(name) {
		if (name) {
			var $newGroup = $('<li><p>' + name + '</p><ul class="b-tags-list" data-group="' + name + '"><li class="b-tag"></li></ul></li>');

			$newGroup.find('.b-tag').html(this.element.find('.b-tags-add').first().clone());
			$newGroup.insertBefore(this.element.find('.b-tags-grouped').children().last());
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
			});

		this.initTagList(this.element.find('.b-tags-list'));
	},

	_create: function() {
		this.page = this.options.page;

		this.bind();
	},

	getUrl: function(action) {
		return this.baseUrl + action + '/' + this.page.id;
	},

	initTagList: function($list) {
		var page = this.page,
			pageTags = this;

		$list.pageTagSearch({
			addTag: function(e, data) {
				pageTags.addTag(data.group, data.tag, data.element);
			},
			removeTag: function(e, tagId) {
				page.removeTag(tagId);
			}
		});
	},

	updateTagList: function() {
		var tagEditor = this;

		$.get(this.getUrl('list'));
	}
});