$.widget('boom.assetTagAutocomplete', $.boom.tagAutocomplete,  {
	url : '/cms/autocomplete/asset_tags',

	_tagSelected : function(tag) {
		if (typeof(tag) === 'object') {
			this._trigger('complete', null, {tag : tag.label});
		} else {
			this._trigger('complete', null, {tag : tag});
		}
	}
});