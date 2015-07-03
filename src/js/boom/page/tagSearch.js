$.widget('boom.pageTagSearch',  {
	tags : [],

	addTag : function(tag) {
		this.tags.push(tag.id);

		$('<li class="b-tag"><span>' + tag.name + '</span><a href="#" class="fa fa-trash-o b-tag-remove" data-tag="' + tag.id + '"></a></li>')
			.insertBefore(this.tagList.children().last());

		this._trigger('addTag', null, {
			group : this.group,
			tag : tag.name
		});

		this.update();
	},

	bind : function() {
		var tagSearch = this;

		this.input
			.pageTagAutocomplete({
				group : this.group,
				complete : function(e, tag) {
					tagSearch.addTag(tag);
				}
			});

		this.element.on('click', '.b-button[title="Add tag"]', function(e) {
			e.preventDefault();

			var $input = $(this).parent().find('input');

			if ($input.val()) {
				$input.pageTagAutocomplete('tagSelected', $input.val());
			}
		});

		this.element.on('click', '.b-tag-remove', function() {
			tagSearch.removeTag($(this));
		});
	},

	_create : function() {
		this.tagList = this.element.is('ul') ? this.element : this.element.find('ul');
		this.input = this.element.find('input');
		this.group = this.element.attr('data-group');

		this.bind();
	},

	removeTag : function($a) {
		var tag = $a.attr('data-tag');

		$a.parent().remove();
		this.tags.splice(this.tags.indexOf(tag));

		this._trigger('removeTag', null, tag);
		this.update();
	},

	update : function() {
		this.input.pageTagAutocomplete('setIgnoreTags', this.tags);
		this._trigger('update', null, {tags : this.tags});
	}
});