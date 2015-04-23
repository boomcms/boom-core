module.exports = function(grunt) {
	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		concat: {
			options: {
				separator: ';',
				process: function(src, filepath) {
				  return src.replace(/@VERSION/g, grunt.config.get('pkg.version'));
				}
			},
			dist: {
	  			src: [
					'bower_components/modernizr/modernizr.js',
					'media/boom/js/string.js',
					'bower_components/jquery/dist/jquery.js',
					'bower_components/jquery-ui/jquery-ui.js',
					'media/boom/js/jquery/ui.splitbutton.js',
					'media/boom/js/jquery/ui.tree.js',
					'bower_components/jgrowl/jquery.jgrowl.js',
					'bower_components/tablesorter/jquery.tablesorter.js',
					'bower_components/datetimepicker/jquery.datetimepicker.js',
					'media/boom/js/boom/plugins.js',
					'media/boom/js/boom/config.js',
					'media/boom/js/boom/loader.js',
					'media/boom/js/boom/notification.js',
					'media/boom/js/boom/core.js',
					'media/boom/js/boom/history2.js',
					'media/boom/js/boom/log.js',
					'media/boom/js/boom/dialog.js',
					'media/boom/js/boom/alert.js',
					'media/boom/js/boom/confirmation.js',
					'bower_components/pushy/js/pushy.js',
					'media/boom/js/boom/tagAutocomplete.js',
					'media/boom/js/boom/page/status.js',
					'media/boom/js/boom/page/page.js',
					'media/boom/js/boom/page/settings.js',
					'media/boom/js/boom/page/editor.js',
					'media/boom/js/boom/page/toolbar.js',
					'media/boom/js/boom/page/tree.js',
					'media/boom/js/boom/page/url.js',
					'media/boom/js/boom/page/tagEditor.js',
					'media/boom/js/boom/page/tagSearch.js',
					'media/boom/js/boom/page/tagAutocomplete.js',
					'media/boom/js/boom/urlEditor.js',
					'media/boom/js/boom/page/featureEditor.js',
					'media/boom/js/boom/page/visibilityEditor.js',
					'media/boom/js/boom/textEditor.js',
					'media/boom/js/boom/chunk.js',
					'media/boom/js/boom/chunk/chunk.js',
					'media/boom/js/boom/chunk/text.js',
					'media/boom/js/boom/chunk/linkset.js',
					'media/boom/js/boom/chunk/feature.js',
					'media/boom/js/boom/chunk/asset.js',
					'media/boom/js/boom/chunk/slideshow.js',
					'media/boom/js/boom/chunk/timestamp.js',
					'media/boom/js/boom/chunk/tag.js',
					'media/boom/js/boom/chunk/slideshow/editor.js',
					'media/boom/js/boom/chunk/linkset/editor.js',
					'media/boom/js/boom/chunk/asset/editor.js',
					'media/boom/js/boom/chunk/pageTags.js',
					'media/boom/js/boom/chunk/pageVisibility.js',
					'media/boom/js/boom/page/title.js',
					'media/boom/js/boom/link/link.js',
					'media/boom/js/boom/link/picker.js',
					'media/boom/js/boom/template/manager.js',
					'media/boom/js/boom/asset/asset.js',
					'media/boom/js/boom/asset/manager.js',
					'media/boom/js/boom/asset/picker.js',
					'media/boom/js/boom/asset/titleFilter.js',
					'media/boom/js/boom/asset/uploader.js',
					'media/boom/js/boom/asset/justify.js',
					'media/boom/js/boom/asset/tagAutocomplete.js',
					'media/boom/js/boom/asset/tagSearch.js',
					'media/boom/js/boom/group/group.js',
					'media/boom/js/boom/group/permissionsEditor.js',
					'media/boom/js/boom/person.js',
					'media/boom/js/boom/peopleManager.js',
					'bower_componenets/jquery-file-upload/js/jquery.fileupload.js',
					'bower_components/wysihtml/dist/wysihtml5x-toolbar.js',
					'media/boom/js/wysihtml5/parser_rules/full.js',
					'media/boom/js/wysihtml5/parser_rules/inline.js',
					'media/boom/js/wysihtml5/commands/insertBoomAsset.js',
					'media/boom/js/wysihtml5/commands/createBoomLink.js',
					'media/boom/js/wysihtml5/commands/cta.js',
					'media/boom/js/wysihtml5/commands/insertSuperscript.js',
					'media/boom/js/wysihtml5/commands/insertSubscript.js',
					'media/boom/js/jquery/jqpagination.js',
					'media/boom/js/boom/page/manager.js'
				],
				dest: 'media/boom/js/cms.js'
			}
		},
		uglify: {
			options: {
				banner: '/*! <%= pkg.name %> - v<%= pkg.version %> (<%= grunt.template.today("yyyy-mm-dd") %>) */\n',
				sourceMap: true
			},
			build: {
				files: {
					'media/boom/js/cms.js': 'media/boom/js/cms.js'
				}
			}
		},
		less: {
			production: {
				options: {
					paths: ["media/boom/css"]
				},
				files: {
					"media/boom/css/cms.css": "media/boom/css/cms.less"
				}
			}
		},
		cssmin: {
			options: {
				shorthandCompacting: false,
				roundingPrecision: -1
			},
			target: {
				files: {
					 'media/boom/css/cms.css': [
						'bower_components/pushy/css/pushy.css',
						'bower_components/datetimepicker/jquery.datetimepicker.css',
						'bower_components/jquery-ui/themes/base/autocomplete.css',
						'bower_components/jquery-ui/themes/base/tabs.css',
						'bower_components/jquery-ui/themes/base/sortable.css',
						'bower_components/jquery-ui/themes/base/core.css',
						'bower_components/jquery-ui/themes/base/button.css',
						'bower_components/jquery-ui/themes/base/draggable.css',
						'bower_components/jquery-ui/themes/base/dialog.css',
						'media/boom/css/libraries/jqpagination.css',
						'media/boom/css/cms.css'
					 ]
				}
			}
		},
		watch: {
			css: {
				files: '**/*.less',
				tasks: ['build']
			},
			js: {
				files: 'media/boom/js/*.js',
				tasks: ['build']
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-cssmin');

	grunt.registerTask('build', ['less', 'concat:dist']);
	grunt.registerTask('dist', ['less', 'cssmin', 'concat:dist', 'uglify']);
	grunt.registerTask('default',['watch']);
};
