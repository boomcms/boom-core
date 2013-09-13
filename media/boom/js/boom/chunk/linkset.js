/**
@class
@name chunkLinkset
@extends $.ui.chunk
@memberOf $.ui
*/
$.widget('ui.chunkLinkset', $.ui.chunk,
	/**
	@lends $.ui.chunkLinkset
	*/
	{

	elements : {},

	/**
	Open a dialog to edit the slected linkset.
	*/
	edit : function(){

		$.boom.log('Linkset chunk slot edit');

		var self = this;

		this.options.treeConfig = $.extend({}, $.boom.config.tree, {
			height: 'auto',
			overflow: 'hidden',
			click: false,
			toggleSelected: false,
			showEdit: false,
			showRemove: true,
			onRemoveClick: function(event){
				var item = $( event.target ).closest( 'li' );
				item.remove();
			},
			iconRemove: 'ui-icon-close'
		});

		this.dialog = $.boom.dialog.open({
			url: this.options.urlPrefix + '/linkset/edit/' + $.boom.page.options.id,
			title: 'Edit linkset',
			id: self.element[0].id + '-boom-dialog',
			width: 400,
			destroy: function(){
				self.destroy();
			},
			treeConfig: this.options.treeConfig,
			open: function(){

				self.elements.currentLinks = $( this ).find('.boom-chunk-linkset-links-set');

				self.elements.internalLinks = $( this ).find('.boom-chunk-linkset-internal-links');

				self._buildList();

			},
			onLoad: function(){
				self._bindEvents();
			},
			callback: function(){
				self
					.insert()
					.done( function(){
						self.destroy();
					});
			}
		});

	},

	/**
	Clone the linkset links for editing.
	*/
	_buildList : function(){

		var self = this, clones = this.element.find('li').not('.boom-chunk-linkset-addlink').clone();

		this.elements.currentLinks.append( clones );

		if ( !this._refresh() ) {

			setTimeout(function(){

				$.boom.log('Check linkset links');

				self.dialog.find('.boom-tabs:first').tabs('options', 'active', 1);
			});
		}
	},

	/**
	Bind button and tree control events.
	*/
	_bindEvents : function(){

		var self = this;
		$.boom.util.page_tree( this.dialog.find('.boom-chunk-linkset-internal-links .boom-tree') )
			.progress( function( page ) {
				var anchor =
					$( '<a>')
						.attr( 'rel', page.page_id )
						.attr( 'href', page.url )
						.text( page.title );
				self._add( anchor );
			});

		$('#boom-chunk-linkset-addlink-external-button').click(function(){

			var
				url = $('#boom-chunk-linkset-addlink-external-url').val().trim(),
				copy = $('#boom-chunk-linkset-addlink-external-copy').val().trim(),
				form = $('#boom-chunk-linkset-addlink-external-form'),
				exp = /^https?:\/\//, m, protocol;

			while( m = exp.exec(url) ) {
				url = url.replace(exp, '');
				protocol = m[0];
			}
			if (!protocol) protocol = 'http://';

			if ( url && copy ) {

				url = protocol + url;

				var anchor = $('<a />', { href: url }).text( copy );

				self._add(anchor);

				form.get(0).reset();
			}

			return false;
		});
	},

	/**
	FIXME: Not sure what this does.
	*/
	_refresh: function(){

		if (!this.elements.currentLinks.children().length) {

			$('#boom-chunk-linkset-urls-valid').hide();
			$('#boom-chunk-linkset-urls-invalid').show();

			return false;
		} else {

			$('#boom-chunk-linkset-urls-valid').show();
			$('#boom-chunk-linkset-urls-invalid').hide();

			return true;
		}
	},

	/**
	Add a new link to the list in the linkset dialog.
	@param {Object} anchor <a> element for the new link.
	*/
	_add: function(anchor) {

		var link = $('<li />').hide().append(anchor);

		this.elements.currentLinks.append(link).tree(this.options.treeConfig);

		this._refresh();

		this.dialog.find('.boom-tabs:first').tabs('option', 'active', 0);

		link.fadeIn(function(){
			$(this).removeAttr('style');
		});
	},

	insert: function(){
		var self = this;

		var data = this.getData();

		if (data.links.length == 0) {
			return this.remove();
		} else {
			return self._save(data);
		}
	},

	/**
	Get the linkset data as an object
	@returns {Object} Simple object containing an array of link objects {links: [ { name: name, uri: uri, target_page_rid: page RID, sequence: sequence }]}
	*/
	getData : function() {

		return this._getData( this.element );
	},

	/**
	Get the linkset data as an object
	@param {Object} element Container element for this linkset.
	@returns {Object} Simple object containing an array of link objects {links: [ { name: name, uri: uri, target_page_rid: page RID, sequence: sequence }]}
	*/
	_getData : function( element ) {

		var links = [];

		element.find('a').each(function(sequence){

			// ensure internal links have no domain attached to them
			var url =
				!this.rel ?
				this.href.safeEscape() :
				this.href.replace(/[a-zA-Z]{4,5}\W{3}[^\/]+/, '').safeEscape();


			var link = {
				title: $(this).text(),
				url: url,
				target_page_id: $(this).attr('rel'),
				sequence: sequence
			};

			links.push( link );
		});

		return { links: links };
	}

});