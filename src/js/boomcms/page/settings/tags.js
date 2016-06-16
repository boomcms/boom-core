(function($, BoomCMS) {
	'use strict';

	$.widget('boom.pageSettingsTags', {
		activeClass: 'active',

		addTag: function($a) {
			var activeClass = this.activeClass,
				group = $a.parents('ul').attr('data-group'),
				tag = $a.find('span:first-of-type').text();

			this.page
				.addTag(group, tag)
				.done(function(tagId) {
					$a.attr('data-tag', tagId)
					.addClass(activeClass);
				});
		},

		addTagGroup: function(name) {
			if (name) {
				var $newGroup = $('<li><h2>' + name + '</h2><ul data-group="' + name + '"></ul></li>');

				this.$list.append($newGroup);

				$newGroup
					.append(this.newTagForm)
					.find('input')
					.focus();
			}
		},

		bind: function() {
			var tagEditor = this,
				page = this.page;

			this.element
				.on('submit', 'form', function(e) {
					e.preventDefault();
				})
				.on('submit', '.b-tags-newgroup form', function() {
					var $input = $(this).find('input[type=text]');

					tagEditor.addTagGroup($input.val());
					$input.val('');
				})
				.on('submit', '.create-tag', function() {
					var $this = $(this),
						group = $this.siblings('ul').attr('data-group'),
						tag = $this.find('input').val();

					tagEditor.createTag(group, tag);
					$this.find('input').val('');
				})
				.on('click', '#b-page-tags a', function(e) {
					e.preventDefault();
						
					tagEditor.toggleTag($(this));
				});
		},

		_create: function() {
			var pageTags = this;

			this.page = this.options.page;
			this.$list = this.element.find('#b-page-tags > ul');
			this.newTagForm = this.element.find('#b-tags-add').html();
			this.tagTemplate = this.element.find('#b-tag-template').html();

			this.$list.find('> li').each(function() {
				$(this).append(pageTags.newTagForm);
			});

			this.bind();
		},

		createTag: function(group, tag) {
			if (group && tag) {
				var $li = $(this.tagTemplate);

				$li.find('span:first-of-type').text(tag);

				this.element
					.find('ul[data-group="' + group + '"]')
					.append($li);

				this.addTag($li.find('a'));
			}
		},

		removeTag: function($a) {
			var activeClass = this.activeClass;

			this.page
				.removeTag($a.attr('data-tag'))
				.done(function() {
					$a.removeClass(activeClass);
				});
		},

		toggleTag: function($a) {
			var funcName = $a.hasClass(this.activeClass) ? 'removeTag' : 'addTag';

			this[funcName]($a);
			$a.blur();
		}
	});
}(jQuery, BoomCMS));
