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

(function($) {

	/**
	@function
	*/
	$.fn.ui = function(opts){
		$.boom.log('Start bind UI events');

		this.find('.boom-tabs').tabs();
		this.find('.boom-datepicker').datetimepicker($.boom.config.datepicker);
		this.find('.boom-tree').tree($.boom.config.tree);

		$.boom.log('Stop bind UI events');

		return this;
	};
})( jQuery );