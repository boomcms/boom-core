/**
@fileOverview Link editor dialog
*/
$.extend( $.boom,
	/** @lends $.boom */
	{
	/**
	Link manager
	@namespace
	*/
	links : {

		/**
		@function
		@example
		var link = {
			url : existing_link.href,
			rid : existing_link.rel,
			title : ( existing_link.textContent || existinglink.innerText )
		};
		opts.link = link;
		$.boom.links
			.picker( opts )
			.done( function( link ){
				existing_link.href = link.url;
				existing_link.rel = link.rid;
			});
		*/
		picker: function( opts ) {

			opts = ( opts ) ? opts : {};

			var complete = new $.Deferred();

			var link_manager_url =
				( opts.page_rid && typeof opts.page_rid != 'undefined' ) ?
				'/cms/chunk/insert_url/' + opts.page_rid :
				'/cms/chunk/insert_url';

			var default_opts =
			/** @ignore */ {
				title: 'Edit link',
				url: link_manager_url,
				width : 600,
				deferred: complete,
				onLoad: function(){
					var self = this;
					var type_selector = $('#b-chunk-linkset-addlink-external-type');
					var external_url = $( '#boom-chunk-linkset-addlink-external-url' );

					if ( opts.link.rid == -1 || opts.link.rid == "" ) {
						var url = opts.link.url;

						if (url.substring(0,7) =='http://' || url.substring(0,8) =='https://' || url.substring(0,1) == '/') {
							url = url.replace('https://', '').replace('http://', '');
							type_selector.val('http');
						}
						else if (url.substring(0,7) =='mailto:') {
							url = url.replace('mailto:', '');
							type_selector.val('mailto');
						}
						else if (url.substring(0,4) =='tel:') {
							url = url.replace('tel:', '');
							type_selector.val('tel');
						}

						if (url != "") {
							external_url.val( url );
							$( 'a[href=#boom-chunk-linkset-addlink-external]' ).trigger('click');
						}

					}

					type_selector.on('change', function() {
						if (external_url.val() == 'http://') {
							external_url.val('');
						}
					});

					this.find('.boom-tree').pageTree({
						onPageSelect : function(page) {
							link = page;
							complete.resolve( page );
							$.boom.dialog.destroy( self );
						}
					});
				},
				callback: function(){

					if ( link.rid == -1 || link.rid == "") {
						var url = link_text = $( '#boom-chunk-linkset-addlink-external-url' ).val();

						switch($('#b-chunk-linkset-addlink-external-type').val()) {
							case 'http':
								if (url.substring(0,7) !='http://' && url.substring(0,8) !='https://' && url.substring(0,1) != '/') {
									url = 'http://' + url;
								}
								break;
							case 'mailto':
								if (url.substring(0,6) != 'mailto:') {
									url = 'mailto:' + url;
								}
								break;
							case 'tel':
								if (url.substring(0,3)) {
									url = 'tel:' + url;
								}
								break;
						}

						link.url = url;
						link.title = link_text;
					}

					complete.resolve( link );
				},
				link : {
					title: '',
					rid: -1,
					url: ''
				}
			};

			opts = $.extend( default_opts, opts );

			var link = opts.link;

			new boomDialog(opts);

			return complete;
		}
	}
});