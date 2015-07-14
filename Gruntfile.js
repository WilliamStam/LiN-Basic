module.exports = function(grunt) {
	require("matchdep").filterDev("grunt-*").forEach(grunt.loadNpmTasks);
	grunt.initConfig({
		concat: {
			js: {
				options: {
					separator: ';',
					stripBanners: true
				},
				src: [
					'vendor/components/jquery/jquery.js',
					'vendor/components/bootstrap/js/bootstrap.js' ,
					'app/_js/script.js'
				],
				dest: 'app/javascript.js',
				nonull: true
			}
		},
		uglify: {
			options: {
				spawn: false,
				banner: '/*! Build date: <%= grunt.template.today("dd-mm-yyyy") %> */\n'
			},
			js: {
				files: {
					'app/javascript.js': ['app/javascript.js']
				}
			}
		},
		less: {
			style: {
				files: {
					"app/style.css": "app/less/style.less"
				}
			}
		},
		cssmin: {
			options: {
				report: "min",
				keepSpecialComments: 0
			},
			target: {
				files: {
					'app/style.css': 'app/style.css'
				}
			}
		},
		watch: {
			js: {
				files: ['js/*.js'],
				tasks: ['concat:js'],
				options: {
					spawn: false,
					livereload: true
				}
			},
			css: {
				files: ['less/*.less'],
				tasks: ['less:style'],
				options: {
					spawn: false,
					livereload: true
				}
			}
		}
	});

	
	


	
	
	grunt.registerTask('jsmin', ['uglify:js']);
	grunt.registerTask('js', ['concat:js']);
	grunt.registerTask('css', ['less:style']);
	grunt.registerTask('build', ['concat:js','less:style', 'uglify:js','cssmin']);
	grunt.registerTask('default', ['watch']);

};