
$.extend( $.boom, {
	/** @lends $.boom */
	
	/**
	Link manager
	@class
	*/
	links : {
		
		/** @function */
		picker: function( opts ) {
			
			opts = ( opts ) ? opts : {};
			
			var link_manager_url =
				( opts.page_rid && typeof opts.page_rid != 'undefined' ) ?
				'/cms/chunk/insert_url/' + opts.page_rid : 
				'/cms/chunk/insert_url';
			
			var default_opts = {
				title: 'Edit link',
				url: link_manager_url,
				onLoad: function(){
					var self = this;
					$.boom.util.page_tree( this.find( '.boom-tree' ) )
						.progress( function( page ) {
							link = page;
							
							complete.resolve( page );

							$.boom.dialog.destroy( self );
						});
				},
				buttons: {
					'✕': function(){
						complete.reject();

						$.boom.dialog.destroy( this );
						
						return false;
					},
					'✔': function(){
						
						if ( link.rid == -1 ) {
							var link_text = $( '#boom-chunk-linkset-addlink-external-url' ).val();
							link.url = link_text;
							link.title = link_text;
						}
						
						complete.resolve( link );

						$.boom.dialog.destroy( this );
						
						return false;
					}
				},
				link : {
					title: '',
					rid: -1,
					url: ''
				}
			};
			
			opts = $.extend( default_opts, opts );
			
			var link = opts.link;
			var complete = new $.Deferred();
			
			$.boom.dialog.open( opts );
			
			return complete;
		}
	}
});