/**
@class
@name $.boom.tagmanager.base.item
*/
$.extend($.boom.tagmanager.base.item.prototype, {
	/** @lends $.boom.tagmanager.base.item */

	/**
	Get???
	*/
	get: function(){

		//this.tagmanager.elements.rightpane.trigger( 'resize.boom' );

		if ( this.buttonManager ) {

			if ( this.buttonManager.show ) {

				$( this.buttonManager.show.join(',') ).show();
			}

			if ( this.buttonManager.hide ) {

				$( this.buttonManager.hide.join(',') ).hide();
			}
		}
	}
});

/**
@class
*/
$.boom.items = $.extend( true, {}, $.boom.tagmanager.items );

/**
@class
*/
$.boom.items.tag = $.extend(true, {}, $.boom.tagmanager.base.item);

$.extend($.boom.items.tag,  {
	/** @lends $.boom.items.tag */

	/**
	@property
	*/
	buttonManager: {

		show: [ '#b-assets-upload' ],
		hide: [ '#boom-tagmanager-save-all' ]
	},

	rid: 0,

	filters: {},

	/** @function */
	get : function( rid ){

		$.boom.log( 'get tag ' + rid );

		var self = this;
		var options = this.tagmanager.options;

		this.rid = rid;

		$.boom.loader.show();

		params =
			'tag=' + rid + '&' +
			'perpage=' + options.perpage + '&' +
			'sortby=' + options.sortby;

		for ( filter in self.filters ) {
			params += '&' + filter + '=' + self.filters[ filter ];
		}

		var url =
			'/cms/' + options.type + '/list'
			+ '?' + params;

		self.tagmanager.options.url = url;

		return self.tagmanager.elements.rightpane
			.find('.b-items-content')
			.sload( url, function(){

				$.boom.tagmanager.base.item.prototype.get.apply( self );

				$.boom.loader.hide();

				self.bind();
				
				$('.b-items-thumbs .thumb').captions($.boom.config.captions);
				
				$.boom.log('Tag items get');
			});
	},

	/** @function */
	bind : function(){
		$.boom.log( 'items.tag.bind()' );

		var self = this;

		var tabsConfig = $.extend({}, $.boom.config.tabs, {

			show: function(event, ui){
			}
		});

		this.tagmanager.elements.rightpane.ui({
			tabs: tabsConfig
		});

		$.boom.events.register('tag.clickAfter', 'tagmanager');

	},

	/** @function */
	edit : function(event){
		event.preventDefault();

		var item = $( event.target ).closest( 'li' ), rid = item.find('a')[0].rel;
		var self = this;

		$.boom.dialog.open({
			url: '/cms/tags/edit/' + rid + '?type=1',
			title: 'Edit tag',
			buttons: {
				Cancel: function(){
					$.boom.dialog.destroy(this);
				},
				Save: function(){
					var dialog = this;
					var data = $( dialog ).find('form').serialize();

					item.find('> a').text( $( '#b-tag-name' ).val() );

					self.save( rid, data)
					.done( function(){

						$.boom.dialog.destroy(dialog);

						$.boom.growl.show('Tag successfully saved.');
					});
				}
			}
		});
	},

	/** @function */
	save: function( tag_id, data ){
		$.boom.loader.show();

		return $.post('/cms/tags/save/' + tag_id, data)
		.done( function(response){

			$.boom.loader.hide();

		});
	},

	/** @function */
	remove : function(event){

		event.preventDefault();

		var item = $( event.target ).closest( 'li' ), rid = item.find('a')[0].rel;

		$.boom.dialog.confirm('Please confirm', 'Are you sure you want to remove this tag? <br /><br /> This will delete the tag from the database and cannot be undone!', function(){

			$.boom.loader.show();

			$.post('/cms/tags/delete/' + rid)
			.done( function(){

				$.boom.loader.hide();

				$.boom.growl.show('Tag successfully removed.');
				item.remove();
			});
		});
	}
});