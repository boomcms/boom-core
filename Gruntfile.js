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
					'public/boom/js/string.js',
					'bower_components/jquery/dist/jquery.js',
					'bower_components/jquery-ui/jquery-ui.js',
					'public/boom/js/jquery/ui.splitbutton.js',
					'public/boom/js/jquery/ui.tree.js',
					'bower_components/jgrowl/jquery.jgrowl.js',
					'bower_components/tablesorter/jquery.tablesorter.js',
					'bower_components/datetimepicker/jquery.datetimepicker.js',
					'public/boom/js/boom/plugins.js',
					'public/boom/js/boom/config.js',
					'public/boom/js/boom/loader.js',
					'public/boom/js/boom/notification.js',
					'public/boom/js/boom/core.js',
					'public/boom/js/boom/history2.js',
					'public/boom/js/boom/log.js',
					'public/boom/js/boom/dialog.js',
					'public/boom/js/boom/alert.js',
					'public/boom/js/boom/confirmation.js',
					'bower_components/pushy/js/pushy.js',
					'public/boom/js/boom/tagAutocomplete.js',
					'public/boom/js/boom/page/status.js',
					'public/boom/js/boom/page/page.js',
					'public/boom/js/boom/page/settings.js',
					'public/boom/js/boom/page/editor.js',
					'public/boom/js/boom/page/toolbar.js',
					'public/boom/js/boom/page/tree.js',
					'public/boom/js/boom/page/url.js',
					'public/boom/js/boom/page/tagEditor.js',
					'public/boom/js/boom/page/tagSearch.js',
					'public/boom/js/boom/page/tagAutocomplete.js',
					'public/boom/js/boom/urlEditor.js',
					'public/boom/js/boom/page/featureEditor.js',
					'public/boom/js/boom/page/visibilityEditor.js',
					'public/boom/js/boom/textEditor.js',
					'public/boom/js/boom/chunk.js',
					'public/boom/js/boom/chunk/chunk.js',
					'public/boom/js/boom/chunk/text.js',
					'public/boom/js/boom/chunk/linkset.js',
					'public/boom/js/boom/chunk/feature.js',
					'public/boom/js/boom/chunk/asset.js',
					'public/boom/js/boom/chunk/slideshow.js',
					'public/boom/js/boom/chunk/timestamp.js',
					'public/boom/js/boom/chunk/tag.js',
					'public/boom/js/boom/chunk/slideshow/editor.js',
					'public/boom/js/boom/chunk/linkset/editor.js',
					'public/boom/js/boom/chunk/asset/editor.js',
					'public/boom/js/boom/chunk/pageTags.js',
					'public/boom/js/boom/chunk/pageVisibility.js',
					'public/boom/js/boom/page/title.js',
					'public/boom/js/boom/link/link.js',
					'public/boom/js/boom/link/picker.js',
					'public/boom/js/boom/template/manager.js',
					'public/boom/js/boom/asset/asset.js',
					'public/boom/js/boom/asset/manager.js',
					'public/boom/js/boom/asset/picker.js',
					'public/boom/js/boom/asset/titleFilter.js',
					'public/boom/js/boom/asset/uploader.js',
					'public/boom/js/boom/asset/justify.js',
					'public/boom/js/boom/asset/tagAutocomplete.js',
					'public/boom/js/boom/asset/tagSearch.js',
					'public/boom/js/boom/group/group.js',
					'public/boom/js/boom/group/permissionsEditor.js',
					'public/boom/js/boom/person.js',
					'public/boom/js/boom/peopleManager.js',
					'bower_componenets/jquery-file-upload/js/jquery.fileupload.js',
					'bower_components/wysihtml/dist/wysihtml5x-toolbar.js',
					'public/boom/js/wysihtml5/parser_rules/full.js',
					'public/boom/js/wysihtml5/parser_rules/inline.js',
					'public/boom/js/wysihtml5/commands/insertBoomAsset.js',
					'public/boom/js/wysihtml5/commands/createBoomLink.js',
					'public/boom/js/wysihtml5/commands/cta.js',
					'public/boom/js/wysihtml5/commands/insertSuperscript.js',
					'public/boom/js/wysihtml5/commands/insertSubscript.js',
					'public/boom/js/jquery/jqpagination.js',
					'public/boom/js/boom/page/manager.js'
				],
				dest: 'public/boom/js/cms.js'
			}
		},
		uglify: {
			options: {
				banner: '/*! <%= pkg.name %> - v<%= pkg.version %> (<%= grunt.template.today("yyyy-mm-dd") %>) */\n',
				sourceMap: true
			},
			build: {
				files: {
					'public/boom/js/cms.js': 'public/boom/js/cms.js'
				}
			}
		},
		less: {
			production: {
				options: {
					paths: ["public/boom/css"]
				},
				files: {
					"public/boom/css/cms.css": "public/boom/css/cms.less"
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
					 'public/boom/css/cms.css': [
						'bower_components/pushy/css/pushy.css',
						'bower_components/datetimepicker/jquery.datetimepicker.css',
						'bower_components/jquery-ui/themes/base/autocomplete.css',
						'bower_components/jquery-ui/themes/base/tabs.css',
						'bower_components/jquery-ui/themes/base/sortable.css',
						'bower_components/jquery-ui/themes/base/core.css',
						'bower_components/jquery-ui/themes/base/button.css',
						'bower_components/jquery-ui/themes/base/draggable.css',
						'bower_components/jquery-ui/themes/base/dialog.css',
						'public/boom/css/libraries/jqpagination.css',
						'public/boom/css/cms.css'
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
				files: 'public/boom/js/*.js',
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
