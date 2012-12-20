
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
				( opts.page_rid && typeof opts.page_rid == 'undefined' ) ? 
				'/cms/chunk/insert_link' : 
				'/cms/chunk/insert_link/' + opts.page_rid;
			
			var treeConfig = $.extend({}, $.boom.config.tree, {
				width: 300,
				maxHeight: 200,
				toggleSelected: true,
				onClick: function(event){
					var $node = $(this);
					var uri = $node.attr('href');
					var page_rid = $node.attr('rel');
					
					link.title = $node.text();
					link.rid = page_rid;
					link.url = uri;

					return false;
				}
			});
			
			var default_opts = {
				title: 'Edit link',
				url: link_manager_url,
				treeConfig: treeConfig,
				buttons: {
					Okay: function(){
						
						if ( link.rid == -1 ) {
							var link_text = $( '#boom-chunk-linkset-addlink-external-url' ).val();
							link.url = link_text;
							link.title = link_text;
						}
						
						complete.resolve( link );

						$.boom.dialog.destroy( this );
						
						return false;
					}
				}
			};
			
			opts = $.extend( default_opts, opts );
			
			var link = {
				title: '',
				rid: -1,
				url: ''
			};
			var complete = new $.Deferred();
			
			$.boom.dialog.open( opts );
			
			return complete;
		}
	}
})