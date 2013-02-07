/**
@class
@name $.boom.tags
*/
$.extend($.boom, {
	/**
	* @class
	* @name $.boom.tags
	*/
	tags : {

		/** @property */
		base_url: '/cms/tags/',

		bind : function(type, id) {
			var self = this, type = type, id = id;

			var container = $('#b-tags');

			// The add tag input box is hidden when the modal window opens.
			// Show it and give it focus when the add button is clicked.
			container.on('click', '#b-tags-add', function(){
				$('#b-tags-add-name').show().focus();
			});

			// Hide the add tag input box when it loses focus.
			container.on('blur', '#b-tags-add-name', function(){
				$('#b-tags-add-name').val('').hide();
			});

			// When hovering over an existing tag show a button to remove the tag from the page.
			// Then hide the button again when the mouse moves away.
			container
			.on('mouseenter', '.b-tags-list li', function(){
				// If the ui-icon and ui-icon-close clases are added in the HTML then the crosses aren't hidden when the modal opens.
				// So we only add these classes when we need to show them.
				$(this)
					.find('a')
					.addClass('ui-icon ui-icon-close')
					.show()
					.end()
					.find('span')
					.addClass('active');
				// Adding the active class changes the margin-left of the text so that it doesn't shift to the right when then delete url becomes visible.
			})
			.on('mouseleave', '.b-tags-list li', function(){
				$(this)
					.find('a')
					.hide()
					.end()
					.find('span')
					.removeClass('active');
			});

			// Remove a tag from the page.
			container.on('click', '.b-tags-remove', function(event){
				event.preventDefault();

				$.boom.loader.show();

				tag = $(this).attr('href');
				$.post(
					$.boom.tags.base_url + type + '/remove/' + id,
					{tag : tag}
					)
					.done(function(){
						$(event.target).closest('li').remove();
						$.boom.loader.hide();
					});
			});

			// Add a tag to the tag.
			var add_input = container.find('#b-tags-add-name');

			add_input
				.autocomplete({
					delay: 200, // Time to wait after keypress before making the AJAX call.
					source: function(request, response){
						$.ajax({
							url: '/cms/autocomplete/tags',
							dataType: 'json',
							data: {
								text : add_input.val(),
								type : (type == 'asset')? 1 : 2
							}
						})
						.done(function(data) {
							response(data)
						});
					},
					select: function(event, ui){
						self.add(type, id, ui.item.value);
					}
				})
				.keypress(function(e){
					// Add a tag when the enter key is pressed.
					// This allows us to add a tag which doesn't already exist.
					if (e.which == 13) {
						self.add(type, id, add_input.val());
					}
				});
		},

		add : function(type, id, tag) {
			var self = this;

			$.boom.loader.show();

			$.post(
				$.boom.tags.base_url + type + '/add/' + id,
				{tag : tag}
				)
				.done(function(){
					$('#b-tags')
					.parent()
					.load( $.boom.tags.base_url + type + '/list/' + id, function(){

						$( this ).ui();
						self.bind(type, id);
					});
				});

			$.boom.loader.hide();
			$('#b-tags-add-name').val('').hide();
		}
	}
});