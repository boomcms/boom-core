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

			self.tagmanager.elements.rightpane
			.find('.b-items-content')
			.sload( url, function(){

				$.boom.tagmanager.base.item.prototype.get.apply( self );

				$.boom.loader.hide();

				self.bind();
			});

		$.boom.log('Tag items get');
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

		$('.b-items-select-checkbox').change(function(){

			var view =
				this.id.replace(/^[a-z]+-([a-z]+)-[0-9]+$/, "$1") == 'list' ? 'thumb' : 'list',
				type =	this.id.replace(/^([a-z]+)-.*$/, "$1"),
				selector = $( '#' + type + '-' + view + '-' + this.id.replace(/[a-z]+-[a-z]+-/, ''));

			if ( $( this ).is(':checked')) {

				selector.attr('checked', 'checked');

			} else {

				selector.removeAttr('checked');
			}

			var amount = $('.b-items-select-checkbox:checked').length;

			var buttons = $( '[id|=b-button-multiaction]' );

			buttons.button( amount > 0 ? 'enable' : 'disable' );

			$( '#b-button-multiaction-edit' ).button( amount > 2 ? 'disable' : 'enable' );
		});

		$('.b-items-list tbody tr, .b-items-thumbs .thumb').hover(
			function(){
				$( this ).addClass( 'ui-state-hover' );
			},
			function(){
				$( this ).removeClass( 'ui-state-hover' );
			}
		);

		$('#b-items-view-thumbs').on( 'click', 'a', function(event){
			event.preventDefault();

			var container = $(this).parent('div');
			var checkbox = container.find('.b-items-select-checkbox');

			container.toggleClass('ui-state-active');

			checkbox.attr('checked', ! checkbox.attr('checked')).change();
		});

		$('.b-items-thumbs .thumb').captions($.boom.config.captions);

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
	},

	/** @function */
	picker: function( options ) {

		var options = ( options ) ? options : {};

		var tags_edited = new $.Deferred();

		$.boom.dialog.open({
			url: '/cms/tags/asset/list/0',
			// cache: true,
			title: 'Asset tags',
			width: 440,
			buttons: {
				Okay: function(){

					var tag_tree = $(this).find( '.boom-tree' );
					var tags = [];

					$.each( tag_tree.find( '.ui-state-active'), function(){
						var rid = parseInt( $(this).attr( 'rel' ), 10 );
						tags.push( rid );
					});

					tags_edited.resolve( tags );
					$( this ).dialog( 'destroy' );
				}
			},
			onLoad: function(){
				// Make the tag editor work.
				$.boom.tags.init({
					type: 'asset',
					id: 0
				});
			}
		});

		return tags_edited;
	}
});