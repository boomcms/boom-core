/**
@fileOverview Core CMS functionality.
*/
/**
@namespace
@name $
*/
$.extend({
	/**
	Namespace for core boom classes and functions.
	@static
	@class
	@name $.boom
	*/
	boom :
		/** @lends $.boom */
		{

		options: {},

		/**
		Boom bootstrap/setup. Initialises boom.config and does some browser detection.
		*/
		setup: function(){

			$.extend(this, { config: window.boomConfig });

			// reference boom from the site window too.
			top.$.boom = $.boom;
		},

		/**
		Initialise boom classes. Create top bar and UI.
		@param {Object} options Boom options. Extends and overrides defaults in boom.config.
		*/
		init: function(options) {
			( options ) && $.extend( this.config, options );
			this.options = options;

			var classes = ['history'];

			for (i in classes) {
				$.boom[classes[i]].init();
			}

			var $topbar = $('#b-topbar');

			if ($topbar.length) {
				$topbar.ui();

				var user_menu = {
					"Profile" : function(){
						var url = '/cms/profile';

						$.boom.dialog.open({
							'url': url,
							'title': 'User profile',
							callback: function() {
								$.post(url, $('#b-people-profile').serialize())
									.done(function() {
										$.boom.growl.show('Profile updated');
									});
							}
						});
					},
					"Logout" : function(){
						top.location = '/cms/logout';
					}
				};

				$('#boom-page-user-menu')
					.splitbutton({
						items: user_menu,
						width: 'auto',
						menuPosition: 'left',
						split: false
					});
			};


			this._init_widgets();
		},

		_init_widgets : function() {
			$('#b-menu').boomMenu({});
			this.loader = $('body').boomLoader({}).data('boomBoomLoader');
		}
	}
});

$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	@class
	*/
	editor: {
		state : function(state) {
			$.boom.loader.show();

			$.post('/cms/editor/state', {state: state}, function() {
				top.location.reload();
				$.boom.loader.hide();
			});
		}
	}
});

$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	Boom utils.
	@class
	@static
	*/
	util :
		/** @lends $.boom.util */
		{
		/**
		@class
		@static
		@name $.boom.util.dom
		*/
		dom :
			/** @lends $.boom.util.dom */
			{
			/**
			Generate a unique ID for a DOM element
			@param {String} prefix Optional prefix. Defaults to 'boom-'
			*/
			uniqueId : function(prefix){

				prefix = prefix || 'b-';
				var id;

				do {
					id = Math.floor(Math.random()*1000);
				} while($('#' + prefix + id).length);

				return (prefix + id);
			},
		}
	}
});

$.extend($.boom,
	/** @lends $.boom */
	{
	/**
	Boom growl notifications.
	@class
	@static
	*/
	growl : {

		/** @function */
		show : function(msg, sticky){

			$.jGrowl(msg, $.extend({}, $.boom.config.growl, {
				sticky: sticky,
				closer: false,
				open: function(elem, message){
					$(this).removeClass('ui-state-highlight').addClass('ui-state-default').find('.message').prepend('<span class="ui-icon ui-icon-check ui-helper-left" />');
				}
			}));
		},

		/** @function */
		hide : function(id){
			$('.jGrowl-notification').trigger('jGrowl.close');
		}

	}

});

$.boom.setup();