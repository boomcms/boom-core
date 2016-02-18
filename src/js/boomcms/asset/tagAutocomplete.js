$.widget('boom.assetTagAutocomplete', $.boom.tagAutocomplete,  {
	url : '/boomcms/autocomplete/asset-tags',

	tagSelected: function(tag) {
		if (typeof(tag) === 'object') {
			this._trigger('complete', null, {tag : tag.label});
		} else {
			this._trigger('complete', null, {tag : tag});
		}
	}
});