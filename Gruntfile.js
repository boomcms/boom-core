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
					'public/js/string.js',
					'bower_components/jquery/dist/jquery.js',
					'bower_components/jquery-ui/jquery-ui.js',
					'public/js/jquery/ui.splitbutton.js',
					'public/js/jquery/ui.tree.js',
					'bower_components/jgrowl/jquery.jgrowl.js',
					'bower_components/tablesorter/jquery.tablesorter.js',
					'bower_components/datetimepicker/jquery.datetimepicker.js',
                                        'bower_components/jquery-file-upload/js/jquery.fileupload.js',
					'public/js/boom/plugins.js',
					'public/js/boom/config.js',
					'public/js/boom/loader.js',
					'public/js/boom/notification.js',
					'public/js/boom/core.js',
					'public/js/boom/history2.js',
					'public/js/boom/log.js',
					'public/js/boom/dialog.js',
					'public/js/boom/alert.js',
					'public/js/boom/confirmation.js',
					'bower_components/pushy/js/pushy.js',
					'public/js/boom/tagAutocomplete.js',
					'public/js/boom/page/status.js',
					'public/js/boom/page/page.js',
					'public/js/boom/page/settings.js',
					'public/js/boom/page/editor.js',
					'public/js/boom/page/toolbar.js',
					'public/js/boom/page/tree.js',
					'public/js/boom/page/url.js',
					'public/js/boom/page/tagEditor.js',
					'public/js/boom/page/tagSearch.js',
					'public/js/boom/page/tagAutocomplete.js',
					'public/js/boom/urlEditor.js',
					'public/js/boom/page/featureEditor.js',
					'public/js/boom/page/visibilityEditor.js',
					'public/js/boom/textEditor.js',
					'public/js/boom/chunk.js',
					'public/js/boom/chunk/chunk.js',
					'public/js/boom/chunk/text.js',
					'public/js/boom/chunk/linkset.js',
					'public/js/boom/chunk/feature.js',
					'public/js/boom/chunk/asset.js',
					'public/js/boom/chunk/slideshow.js',
					'public/js/boom/chunk/timestamp.js',
					'public/js/boom/chunk/tag.js',
					'public/js/boom/chunk/slideshow/editor.js',
					'public/js/boom/chunk/linkset/editor.js',
					'public/js/boom/chunk/asset/editor.js',
					'public/js/boom/chunk/pageTags.js',
					'public/js/boom/chunk/pageVisibility.js',
					'public/js/boom/page/title.js',
					'public/js/boom/link/link.js',
					'public/js/boom/link/picker.js',
					'public/js/boom/template/manager.js',
					'public/js/boom/asset/asset.js',
					'public/js/boom/asset/manager.js',
					'public/js/boom/asset/picker.js',
					'public/js/boom/asset/titleFilter.js',
					'public/js/boom/asset/uploader.js',
					'public/js/boom/asset/justify.js',
					'public/js/boom/asset/tagAutocomplete.js',
					'public/js/boom/asset/tagSearch.js',
					'public/js/boom/group/group.js',
					'public/js/boom/group/permissionsEditor.js',
					'public/js/boom/person.js',
					'public/js/boom/peopleManager.js',
					'bower_components/wysihtml/dist/wysihtml5x-toolbar.js',
					'public/js/wysihtml5/parser_rules/full.js',
					'public/js/wysihtml5/parser_rules/inline.js',
					'public/js/wysihtml5/commands/insertBoomAsset.js',
					'public/js/wysihtml5/commands/createBoomLink.js',
					'public/js/wysihtml5/commands/cta.js',
					'public/js/wysihtml5/commands/insertSuperscript.js',
					'public/js/wysihtml5/commands/insertSubscript.js',
					'public/js/jquery/jqpagination.js',
					'public/js/boom/page/manager.js'
				],
				dest: 'public/js/cms.js'
			}
		},
		uglify: {
			options: {
				banner: '/*! <%= pkg.name %> - v<%= pkg.version %> (<%= grunt.template.today("yyyy-mm-dd") %>) */\n',
				sourceMap: true
			},
			build: {
				files: {
					'public/js/cms.js': 'public/js/cms.js'
				}
			}
		},
		less: {
			production: {
				options: {
					paths: ["public/css"]
				},
				files: {
					"public/css/cms.css": "public/css/cms.less"
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
					 'public/css/cms.css': [
						'bower_components/pushy/css/pushy.css',
						'bower_components/datetimepicker/jquery.datetimepicker.css',
						'bower_components/jquery-ui/themes/base/autocomplete.css',
						'bower_components/jquery-ui/themes/base/tabs.css',
						'bower_components/jquery-ui/themes/base/sortable.css',
						'bower_components/jquery-ui/themes/base/core.css',
						'bower_components/jquery-ui/themes/base/button.css',
						'bower_components/jquery-ui/themes/base/draggable.css',
						'bower_components/jquery-ui/themes/base/dialog.css',
						'public/css/libraries/jqpagination.css',
						'public/css/cms.css'
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
				files: 'public/js/*.js',
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
