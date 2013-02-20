
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

	srid: 0,

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

		options.url = url;

		return self.tagmanager.elements.rightpane
			.find('.b-items-content')
			.sload( url, function(){

				$.boom.loader.hide();

				self.tagmanager.elements.rightpane.ui();
				
				$.boom.events.register('tag.clickAfter', 'tagmanager');
				
				$('.b-items-thumbs .thumb').captions($.boom.config.captions);
				
				$.boom.log('Tag items get');
			});
	}
});