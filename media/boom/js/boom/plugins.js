/**
@fileOverview jQuery plugins written specifically for Boom.
*/
/**
boom.plugins.js
@author Hoop Associates
*/

/**
@namespace
@name $.fn
*/

(function( $ ){
	/**
	@function
	*/
	$.size = function(obj){

		if ( typeof obj === 'object' ) {

			var size = 0, key;

			for (key in obj) {

				if ( obj.hasOwnProperty( key ) ) {

					size++;
				}
			}

			return size;

		} else {

			return $( obj ).size();
		}
	};

	/**
	@function
	*/
	$.fn.ui = function(opts){

		opts = $.extend({
			tabs: $.boom.config.tabs,
			button: $.boom.config.button,
			tree: $.boom.config.tree,
			sortable: $.boom.config.sortable,
			datepicker: $.boom.config.datepicker
		}, opts);

		var elems = '.boom-button, .boom-tabs, .boom-tree, .boom-sortable, .boom-datepicker';

		$.boom.log('Start bind UI events');

		this.find(elems).each(function(){

			var elem = $(this), types = [];

			$.each(this.className.split(' '), function(){

				if (/boom/.test(this)) {

					types.push(this.replace(/boom-/, ''));
				}
			});

			$.each(types, function(){
				switch(this.toString()) {
					case 'button' :
						var primary = elem.attr( 'data-icon' );
						var secondary = elem.attr( 'data-icon-secondary' );
						opts.button = $.extend( opts.button, {
							icons: { primary: primary, secondary: secondary },
							text: false,
							label: $.trim( elem.text() )
						});
						elem.button(opts.button);
					break;
					case 'tabs' :
						elem.tabs(opts.tabs);
					break;
					case 'tree' :
						elem.tree($.extend({}, opts.tree, {
							border: !(/tree-noborder/).test(elem[0].className)
						}));
					break;
					case 'sortable' :
						elem.sortable($.extend({}, opts.sortable, {
							axis: (/sortable-y/.test(elem[0].className) ? 'y' : 'x')
						}));
					break;
					case 'datepicker' :
						elem.datetimepicker(opts.datepicker);
					break;
				};
			});
		});

		$.boom.log('Stop bind UI events');

		return this;
	};
})( jQuery );