$.widget('ui.chunkCalendar', $.ui.chunk, {
	edit: function() {
		var chunk = this,
			options = this.getOptions(),
			selected,
			dialog;

		dialog = new boomDialog({
			url: '/boomcms/page/' + this.options.currentPage.id + '/chunk/edit?type=calendar&slotname=' + this.options.name,
			width: 700,
			title: 'Select dates',
			onLoad: function() {
				var $content = dialog.contents.find('.content'),
					$textarea = dialog.contents.find('textarea'),
					$calendar = dialog.contents.find('.calendar'),
					content = dialog.contents.find('textarea[name=dates]').val();

				chunk.dates = content ? $.parseJSON(content) : {};

				if (chunk.dates.length === 0) {
					chunk.dates = {};
				}

				$calendar.datepicker({
					dateFormat: "yy-mm-dd",
					onSelect: function(dateText, picker) {
						selected = dateText;

						$content.css('visibility', 'visible');
						$textarea.val(chunk.dates[selected]).focus();
					},
					beforeShowDay: function(date) {
						var year = date.getFullYear(),
							month = ('0' + (date.getMonth() + 1)).slice(-2),
							day = ('0' + date.getDate() ).slice(-2),
							formatted = year + '-' + month + '-' + day,
							className = formatted in chunk.dates ? 'hastext' : '';

						return [true, className];
					}
				});

				$textarea.on('keyup', function() {
					var val = $(this).val();

					if (val) {
						chunk.dates[selected] = val;
						$calendar.find('.ui-state-active').parent().addClass('hastext');
					} else {
						chunk.removeDate(selected);
						$calendar.find('.ui-state-active').parent().removeClass('hastext');
					}
				});

				dialog.contents.on('click', '.remove', function() {
					chunk.removeDate(selected);
					$calendar.find('.ui-state-active').parent().removeClass('hastext');
					$textarea.val('').blur();
					$content.css('visibility', 'hidden');
				});
			}
		})
		.done(function() {
			Object.keys(chunk.dates).length > 0 ? chunk._save() : chunk.remove();
		})
		.fail(function() {
			chunk.destroy();
		});
	},

	getData: function() {
		return {content: this.dates};
	},

	getOptions: function() {
		return {};
	},

	removeDate: function(date) {
		delete this.dates[date];
	}
});
