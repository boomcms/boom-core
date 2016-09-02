(function($, BoomCMS) {
	'use strict';

	$.widget('boom.assetTagSearch',  {
		assets: new BoomCMS.Collections.Assets(),
		tags : [],

		addTag: function(tag) {
			this.tags.push(tag);

			var $newTag = $('<li class="b-tag"><span>' + tag + '</span><a href="#" class="fa fa-trash-o b-tag-remove" data-tag="' + tag + '"></a></li>');

			if (this.tagList.children().length) {
				$newTag.insertBefore(this.tagList.children().last());
			} else {
				$newTag.prependTo(this.tagList);
			}

			this._trigger('addTag', null, tag);
			this.update();
		},

		autocompleteSource: function(request, response) {
			this.assets.getTags().done(function(tags) {
				response(tags);
			});
		},

		bind: function() {
			var tagSearch = this;

			this.assets.getTags().done(function(tags) {
				tagSearch.element.find('input[type=text]').autocomplete({
					source: tags,
					minLength: 0,
					select: function(event, ui) {
						event.preventDefault();

						tagSearch.element.find('input[type=text]').val('');
						tagSearch.addTag(ui.item.value);
					}
				});
			});

			this.element
				.find('button')
				.on('click', function(e) {
					e.preventDefault();

					tagSearch.addTag(tagSearch.input.val());
					tagSearch.input.val('');
				})
				.end()
				.on('click', '.b-tag-remove', function() {
					tagSearch.removeTag($(this));
				})
				.on('keypress', function(e) {
					// Add a tag when the enter key is pressed.
					// This allows us to add a tag which doesn't already exist.
					if (e.which === $.ui.keyCode.ENTER && tagSearch.element.val()) {
						e.preventDefault();

						var tags, i;

						// If the text entered contains commas then it will be treated as a list of tags with each one 'completed'
						tags = tagSearch.element.val().split(',');

						for (i = 0; i < tags.length; i++) {
							tagSearch.tagSelected(tags[i]);
						}

						tagSearch.element.val('');
						tagSearch.element.autocomplete('close');
					}
				});
		},

		_create: function() {
			this.tagList = this.element.find('ul');
			this.input = this.element.find('input');

			this.bind();
		},

		removeTag: function($a) {
			var tag = $a.attr('data-tag');

			$a.parent().remove();
			this.tags.splice(this.tags.indexOf(tag));

			this._trigger('removeTag', null, tag);
			this.update();
		},

		update: function() {
			this._trigger('update', null, {tags : this.tags});
		}
	});
}(jQuery, BoomCMS));
