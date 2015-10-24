module.exports = function(grunt) {
	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		autoprefixer: {
			options: {
			},
			no_dest: {
				src : 'public/css/cms.css'
			}
		},
		copy: {
			main: {
				files: [
					{
						expand: true,
						flatten: true,
						src: ['bower_components/fontawesome/fonts/*'],
						dest: 'public/fonts/',
						filter: 'isFile'
					},
					{
						expand: true,
						flatten: true,
						src: ['bower_components/leaflet/dist/images/*'],
						dest: 'public/images/',
						filter: 'isFile'
					}
				]
			}
		},
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
					'bower_components/jquery/dist/jquery.js',
					'bower_components/jquery-ui/jquery-ui.js',
					'src/js/jquery/ui.tree.js',
					'src/js/string.js',
					'bower_components/jgrowl/jquery.jgrowl.js',
					'bower_components/tablesorter/jquery.tablesorter.js',
					'bower_components/datetimepicker/jquery.datetimepicker.js',
					'bower_components/blueimp-canvas-to-blob/js/canvas-to-blob.js',
					'bower_components/jquery-file-upload/js/jquery.fileupload.js',
					'bower_components/leaflet/dist/leaflet.js',
					'bower_components/caman/dist/caman.full.js',
					'bower_components/jcrop/js/Jcrop.js',
					'src/js/boom/plugins.js',
					'src/js/boom/config.js',
					'src/js/boom/loader.js',
					'src/js/boom/notification.js',
					'src/js/boom/core.js',
					'src/js/boom/history.js',
					'src/js/boom/log.js',
					'src/js/boom/dialog.js',
					'src/js/boom/alert.js',
					'src/js/boom/confirmation.js',
					'bower_components/pushy/js/pushy.js',
					'src/js/boom/tagAutocomplete.js',
					'src/js/boom/page/status.js',
					'src/js/boom/page/page.js',
					'src/js/boom/page/settings.js',
					'src/js/boom/page/editor.js',
					'src/js/boom/page/toolbar.js',
					'src/js/boom/page/tree.js',
					'src/js/boom/page/tagSearch.js',
					'src/js/boom/page/tagAutocomplete.js',
					'src/js/boom/page/url.js',
					'src/js/boom/page/settings/default.js',
					'src/js/boom/page/settings/feature.js',
					'src/js/boom/page/settings/visibility.js',
					'src/js/boom/page/settings/tags.js',
					'src/js/boom/page/settings/urls.js',
					'src/js/boom/page/settings/children.js',
					'src/js/boom/page/settings/navigation.js',
					'src/js/boom/page/settings/template.js',
					'src/js/boom/page/settings/drafts.js',
					'src/js/boom/page/settings/relations.js',
					'src/js/boom/textEditor.js',
					'src/js/boom/chunk.js',
					'src/js/boom/chunk/chunk.js',
					'src/js/boom/chunk/text.js',
					'src/js/boom/chunk/linkset.js',
					'src/js/boom/chunk/feature.js',
					'src/js/boom/chunk/asset.js',
					'src/js/boom/chunk/slideshow.js',
					'src/js/boom/chunk/timestamp.js',
					'src/js/boom/chunk/tag.js',
					'src/js/boom/chunk/slideshow/editor.js',
					'src/js/boom/chunk/linkset/editor.js',
					'src/js/boom/chunk/asset/editor.js',
					'src/js/boom/chunk/pageTags.js',
					'src/js/boom/chunk/pageVisibility.js',
					'src/js/boom/chunk/link.js',
					'src/js/boom/chunk/location.js',
					'src/js/boom/chunk/location/editor.js',
					'src/js/boom/chunk/html.js',
					'src/js/boom/page/title.js',
					'src/js/boom/link/link.js',
					'src/js/boom/link/picker.js',
					'src/js/boom/template/manager.js',
					'src/js/boom/asset/asset.js',
					'src/js/boom/asset/editor.js',
					'src/js/boom/asset/manager.js',
					'src/js/boom/asset/picker.js',
					'src/js/boom/asset/titleFilter.js',
					'src/js/boom/asset/uploader.js',
					'src/js/boom/asset/justify.js',
					'src/js/boom/asset/tagAutocomplete.js',
					'src/js/boom/asset/tagSearch.js',
					'src/js/boom/asset/selection.js',
					'src/js/boom/group/group.js',
					'src/js/boom/group/permissionsEditor.js',
					'src/js/boom/person.js',
					'src/js/boom/peopleManager.js',
					'src/js/boom/imageEditor.js',
					'src/js/bom/approvals.js',
					'bower_components/wysihtml/dist/wysihtml5x-toolbar.js',
					'src/js/wysihtml5/parser_rules/full.js',
					'src/js/wysihtml5/parser_rules/inline.js',
					'src/js/wysihtml5/commands/insertBoomAsset.js',
					'src/js/wysihtml5/commands/createBoomLink.js',
					'src/js/wysihtml5/commands/cta.js',
					'src/js/wysihtml5/commands/insertSuperscript.js',
					'src/js/wysihtml5/commands/insertSubscript.js',
					'src/js/jquery/jqpagination.js',
					'src/js/boom/page/manager.js'
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
					'public/js/cms.min.js': 'public/js/cms.js'
				}
			}
		},
		less: {
			production: {
				options: {
					paths: ["src/css"]
				},
				files: {
					"public/css/cms.css": "src/css/cms.less",
					"public/css/inpage.css": "src/css/inpage.less"
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
					'public/css/inpage.css': [
						'public/css/inpage.css'
					],
					'public/css/cms.css': [
						'bower_components/normalize.css/normalize.css',
						'bower_components/datetimepicker/jquery.datetimepicker.css',
						'bower_components/jquery-ui/themes/base/autocomplete.css',
						'bower_components/leaflet/dist/leaflet.css',
						'bower_components/jcrop/css/Jcrop.css',
						'src/css/libraries/jqpagination.css',
						'public/css/cms.css'
					 ]
				}
			}
		},
		watch: {
			css: {
				files: 'src/css/**/*.less',
				tasks: ['build-css']
			},
			js: {
				files: 'src/js/**/*.js',
				tasks: ['build-js']
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-autoprefixer');
	grunt.loadNpmTasks('grunt-contrib-copy');

	grunt.registerTask('build-css', ['less', 'autoprefixer:no_dest', 'cssmin']);
	grunt.registerTask('build-js', ['concat:dist', 'uglify']);
	grunt.registerTask('dist', ['copy', 'build-css', 'build-js']);
	grunt.registerTask('default',['watch']);
};
