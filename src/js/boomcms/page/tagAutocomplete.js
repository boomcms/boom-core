$.widget('boom.pageTagAutocomplete', $.boom.tagAutocomplete,  {
	url : '/boomcms/autocomplete/page-tags',

	tagSelected : function(tag) {
		if (typeof(tag) === 'object') {
			// A tag which already exists has been selected - we have a tag ID.
			this._trigger('complete', null, {
				id : tag.value,
				name : tag.label
			});
		} else {
			// A new tag has been created - there's no tag ID.
			this._trigger('complete', null, {
				id : 0,
				name : tag
			});
		}
	}
});