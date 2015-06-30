(function(wysihtml5) {
	wysihtml5.commands.insertBoomAsset = {
		exec: function(composer, command, value) {
			value = typeof(value) === "object" ? value : { src: value };

			var doc = composer.doc,
				asset = this.state(composer),
				textNode,
				parent;

			if (asset) {
				// Image already selected, set the caret before it and delete it
				composer.selection.setBefore(asset);
				parent = asset.parentNode;
				parent.removeChild(asset);

				// and it's parent <a> too if it hasn't got any other relevant child nodes
				wysihtml5.dom.removeEmptyTextNodes(parent);
				if (parent.nodeName === "A" && !parent.firstChild) {
					composer.selection.setAfter(parent);
					parent.parentNode.removeChild(parent);
				}

				// firefox and ie sometimes don't remove the image handles, even though the image got removed
				wysihtml5.quirks.redraw(composer.element);
				return;
			} else {
				this._select_asset(composer)
					.done(function(html) {
						composer.selection.insertNode($(html)[0]);
					});
			}
		},

		state: function(composer) {
			var selectedNode = composer.selection.getSelectedNode(),
				$selectedNode = $(selectedNode),
				matchingChildren;

			if ( ! selectedNode) {
				return false;
			}

			if ($selectedNode.prop("tagName") === 'IMG') {
				return selectedNode;
			}

			if ($selectedNode.prop('tagName') === 'A' && $selectedNode.attr('href').match(/asset\/(thumb|view|get_asset)\/([0-9]+)/)) {
				return selectedNode;
			}

			if ($selectedNode.find('img').length) {
				return selectedNode;
			}

			matchingChildren = $selectedNode.find('a').filter(function() {
				var href = $(this).attr('href');

				return href && href.match(/asset\/(thumb|view|get_asset)\/([0-9]+)/);
			}).length;

			if (matchingChildren > 0) {
				return selectedNode;
			}

			return false;
		},

		/**
		@function
		@returns {Deferred}
		*/
		_select_asset : function(composer) {
			var self = this,
				asset_embed = new $.Deferred();

			$(composer).trigger('before:boomdialog');

			new boomAssetPicker()
				.done(function(asset_id) {
					if (asset_id > 0) {
						$.get('/asset/embed/' + asset_id)
							.done(function(response) {
								asset_embed.resolve(response);
							})
							.always(function() {
								asset_embed.reject();
							});
					}

				})
				.always(function() {
					$(composer).trigger('after:boomdialog');
				});

			return asset_embed;
		}
	};
})(wysihtml5);