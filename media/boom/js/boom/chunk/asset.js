/**
@class
@name chunkAsset
@extends $.ui.chunk
@memberOf $.ui
*/
$.widget('ui.chunkAsset', $.ui.chunk,
	/**
	@lends $.ui.chunkAsset
	*/
	{

	/**
	Initialise the caption and asset editor
	*/
	_edit : function() {
		var self = this;

		self.elements = this._get_elements();
		self.asset = this._get_asset_details();

		this.originals = this.element.children().clone(true);

		if (self.elements.caption.length || self.elements.link.length || self.elements.title.length) {
			new boomChunkAssetEditor(this.options.page, this.options.name)
				.done(function(chunkData) {

				});
		} else {
			self._edit_asset(self.elements.asset);
		}
	},

	/**
	Edit the asset
	@param {Object} $caption Caption node
	@returns {Deferred}
	*/
	_edit_asset : function() {

		var self = this, asset_selected = new $.Deferred();

		asset_selected
			.fail(function() {
				$.boom.log('asset chunk cancelled');
				self.destroy();
			});

		new boomAssetPicker(self.asset.asset_id)
			.done(function(asset_id) {
				self.asset.asset_id = asset_id;

				self.insert();
				self.destroy();
			})
			.fail( function() {
				self.remove();
			});
	},

	/**
	 @function
	 */
	 _get_elements: function() {
		var asset_id = this.element.attr('data-boom-target');
		var elements = {};

		var img = this.element.find('img');
		var a = this.element.find('a');

		var regExp = new RegExp("asset\/(thumb|view|download)\/" + asset_id);

		elements.asset = this.element.find('.asset-target');
		elements.link = this.element.find('.asset-link');
		elements.caption = this.element.find('.asset-caption');
		elements.title = this.element.find('.asset-title');

		if (! elements.asset.length) {
			if (img.length && regExp.test(img.attr('src'))) {
				elements.asset = img;
			}
			else if (a.length && regExp.test(a.attr('href'))) {
				elements.asset = a;
			}

			if ( ! elements.asset.length) {
				elements.asset = this.element;
			}
		}

		if ( ! elements.link.length && a.length && elements.asset != a && a.attr('href') && a.attr('href') != '#' && ! regExp.test(a.attr('href'))) {
			elements.link = a;
		}

		return elements;
	 },

	/**
	@function
	*/
	_get_asset_details: function(){
		var asset = {
			asset_id : this.element.attr('data-boom-target'),
			title : this.elements.title.text(),
			caption : this.elements.caption.text(),
			url : this.elements.link.attr('href')
		};

		return asset;
	},

	/**
	Asset editor.
	*/
	edit : function() {

		var self = this;

		this._edit();

		$.boom.log('Asset chunk slot edit ' + self.asset.asset_id);


//		if (this.element != self.elements.asset) {
//			self.elements.asset
//				.on( 'click', function(event) {
//					event.preventDefault();
//
//					self._edit_asset(self.elements.asset);
//					return false;
//				});
//		}

//		this.element.on('click', function(event) {
//			event.preventDefault();
//
//			self._edit_asset(self.elements.asset);
//			return false;
//		});
	},

	/**
	Insert selected asset into the page
	*/
	insert : function() {
		var self = this;

		$.boom.log( 'inserting asset' + self.asset.asset_id );

		return self._save();
	},

	/**
	Get the RID for this asset.
	@returns {Int} Asset RID
	*/
	getData: function() {
		var rid = this.asset.asset_id;

		rid = (rid == 0) ? null : rid;

		return {
			asset_id : rid,
			title : this.asset.title,
			caption : this.asset.caption,
			url : this.asset.url
		};
	}
});