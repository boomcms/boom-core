$.widget('boom.assetTagSearch',  {
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

	bind: function() {
		var tagSearch = this;

		this.input
			.assetTagAutocomplete({
				complete: function(e, data) {
					tagSearch.addTag(data.tag);
				}
			});

		this.element.find('button').on('click', function(e) {
			e.preventDefault();

			tagSearch.addTag(tagSearch.input.val());
			tagSearch.input.val('');
		});

		this.element.on('click', '.b-tag-remove', function() {
			tagSearch.removeTag($(this));
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
		this.input.assetTagAutocomplete('setIgnoreTags', this.tags);
		this._trigger('update', null, {tags : this.tags});
	}
});