/* jshint node:true */
module.exports = function( grunt ) {
	var _ = require( 'lodash' );

	// Load all Grunt tasks
	require( 'load-grunt-tasks' )( grunt );

	// Project configuration.
	grunt.initConfig({
		pkg: grunt.file.readJSON( 'package.json' ),
		watch: {
			css: {
				options: {
					atBegin: true
				},
				files: 'assets/sass/**/*.scss',
				tasks: [ 'sass', 'csscomb:style' ]
			}
		},
		sass: {
			theme: {
				options: {
					outputStyle: 'nested'
				},
				files: {
					'src/style.css': 'assets/sass/style.scss'
				}
			}
		},
		csscomb: {
			style: {
				options: {
					config: 'csscomb.json'
				},
				files: {
					'src/style.css': ['src/style.css']
				}
			}
		},
		jshint: {
			options: grunt.file.readJSON( '.jshintrc' ),
			grunt: {
				src: [
					'Gruntfile.js'
				]
			},
			theme: {
				src: [
					'src/js/**/*.js',
					'!src/js/**/*.min.js',
					'!src/js/libs/**/*.js',
					'src/inc/customizer/js/customizer.js',
					'src/inc/builder/core/js/**/*.js',
					'src/inc/builder/sections/js/**/*.js'
				]
			}
		},
		concat: {
			options: {
				separator: ';'
			},
			cycle2: {
				src: [
					'src/js/libs/cycle2/jquery.cycle2.js',
					'src/js/libs/cycle2/jquery.cycle2.center.js',
					'src/js/libs/cycle2/jquery.cycle2.swipe.js'
				],
				dest: 'src/js/libs/cycle2/jquery.cycle2.min.js'
			}
		},
		uglify: {
			options: {
				preserveComments: 'some'
			},
			libs: {
				files: {
					// Cycle2 source filename already has '.min' because of concat
					'src/js/libs/cycle2/jquery.cycle2.min.js': ['src/js/libs/cycle2/jquery.cycle2.min.js'],
					'src/js/libs/fitvids/jquery.fitvids.min.js': ['src/js/libs/fitvids/jquery.fitvids.js']
				}
			},
			theme: {
				files: {
					'src/js/global.min.js': ['src/js/global.js']
				}
			},
			customizer: {
				files:{
					'src/inc/customizer/js/customizer-preview.min.js': ['src/inc/customizer/js/customizer-preview.js'],
					'src/inc/customizer/js/customizer-sections.min.js': ['src/inc/customizer/js/customizer-sections.js']
				}
			},
			admin: {
				files:{
					'src/js/admin/edit-page.min.js': ['src/js/admin/edit-page.js'],
					'src/inc/gallery-slider/gallery-slider.min.js': ['src/inc/gallery-slider/gallery-slider.js']
				}
			}
		},
		shell: {
			googlefonts: {
				command: [
					'php -f assets/google-fonts-array.php'
				].join('&&')
			}
		},
		makepot: {
			theme: {
				options: {
					cwd: 'src',
					potFilename: 'make.pot',
					domainPath: '/languages',
					type: 'wp-theme',
					exclude: [],
					processPot: function( pot, options ) {
						pot.headers['report-msgid-bugs-to'] = 'https://thethemefoundry.com/support/';
						pot.headers['last-translator'] = 'The Theme Foundry';
						pot.headers['language-team'] = 'The Theme Foundry';
						return pot;
					}
				}
			}
		},
		copy: {
			build: {
				files: [
					{
						expand: true,
						cwd: 'src/',
						src: [
							'**/*',
							'!**/.{svn,git}/**',
							'!**/.DS_Store/**'
						],
						dest: 'dist/temp'
					}
				]
			},
			googlefonts: {
				files: [
					{
						expand: true,
						cwd: 'assets/temp/',
						src: [
							'google-fonts.php'
						],
						dest: 'src/inc/customizer'
					}
				]
			}
		},
		compress: {
			build: {
				options: {
					archive: 'dist/<%= pkg.name %>-<%= pkg.version %>.zip',
					mode: 'zip'
				},
				files: [
					{
						expand: true,
						src: ['**/*'],
						dest: '<%= pkg.name %>',
						cwd: 'dist/temp'
					}
				]
			}
		},
		clean:{
			build: {
				src: [ 'dist/temp' ]
			},
			assets: {
				src: [ 'assets/temp' ]
			}
		},
		replace: {
			styleVersion: {
				src: [
					'assets/sass/header.scss',
					'src/style.css'
				],
				overwrite: true,
				replacements: [ {
					from: /^.*Version:.*$/m,
					to: ' * Version:     <%= pkg.version %>'
				} ]
			},
			functionsVersion: {
				src: [
					'src/functions.php'
				],
				overwrite: true,
				replacements: [ {
					from: /^define\( 'TTFMAKE_VERSION'.*$/m,
					to: 'define( \'TTFMAKE_VERSION\', \'<%= pkg.version %>\' );'
				} ]
			},
			readmeVersion: {
				src: [
					'readme.md'
				],
				overwrite: true,
				replacements: [ {
					from: /^\* \*\*Stable version:\*\* .*$/m,
					to: '* **Stable version:** <%= pkg.version %>'
				} ]
			}
		},
		bump: {
			options: {
				files: [ 'package.json' ],
				updateConfigs: [ 'pkg' ],
				commit: false
			}
		},
		other: {
			changelog: 'src/changelog.md'
		},
		yaml: {
			fontawesome: {
				files: [
					{
						expand: true,
						cwd: 'assets',
						src: 'icons*.yml',
						dest: 'assets/temp'
					}
				]
			}
		},
		json_massager: {
			fontawesome: {
				modifier: function( json ) {
					var icons = json.icons,
						newObj = {};

					_.forEach( icons, function( data ) {
						_.forEach( data.categories, function( category ) {
							if ( 'undefined' === typeof newObj[category] ) {
								newObj[category] = [];
							}
							var icon = {
								name: data.name,
								id: 'fa-' + data.id,
								unicode: data.unicode
							};
							newObj[category].push( icon );
						} );
					} );

					_.forEach( newObj, function( category ) {
						category.sort( function( a, b ) {
							if (a.name.toLowerCase() > b.name.toLowerCase()) {
								return 1;
							}
							if (a.name.toLowerCase() < b.name.toLowerCase()) {
								return -1;
							}
							return 0;
						} );
					} );

					return newObj;
				},
				files: {
					'assets/temp/fontawesome.json': [ 'assets/temp/icons*.json' ]
				}
			},
			googlefonts: {
				modifier: function( json ) {
					var fonts = json.items,
						newObj = {};

					_.forEach( fonts, function( data ) {
						var label = data.family,
							font = {
								label: label,
								variants: data.variants.sort(),
								subsets: data.subsets.sort(),
								category: data.category
							};

						newObj[label] = font;
					} );

					return newObj;
				},
				files: {
					'assets/temp/googlefonts.json': [ 'assets/temp/googlefontsdata.json' ]
				}
			}
		},
		json: {
			fontawesome: {
				options: {
					namespace: 'ttfmakeIconObj',
					processName: function( filename ) {
						return filename.toLowerCase();
					}
				},
				src: [ 'assets/temp/fontawesome.json' ],
				dest: 'src/inc/formatting/icon-picker/icons.js'
			}
		},
		curl: {
			googlefonts: {
				apikey: '',
				src: 'https://www.googleapis.com/webfonts/v1/webfonts?sort=alpha&key=<%= curl.googlefonts.apikey %>',
				dest: 'assets/temp/googlefontsdata.json'
			}
		},
		prompt: {
			googlefonts: {
				options: {
					questions: [
						{
							config: 'curl.googlefonts.apikey',
							type: 'input',
							message: 'Enter your Google Fonts API key'
						}
					]
				}
			}
		}
	});

	// Top level function to build a new release
	grunt.registerTask( 'release', function( releaseType ) {
		if ( 'minor' !== releaseType && 'major' !== releaseType && 'patch' !== releaseType ) {
			grunt.fail.fatal( 'Please specify the release type (e.g., "grunt release:patch")' );
		} else {
			// Check to make sure the log exists
			grunt.task.run( 'log:' + releaseType );

			// Bump the version numbers
			grunt.task.run( 'bumpto:' + releaseType );

			// Create the .pot file
			grunt.task.run( 'makepot' );

			// Build the SASS and scripts
			grunt.task.run( 'default' );

			// Process the icons file
			grunt.task.run( 'fontawesome' );

			// Update the Google Fonts array
			grunt.task.run( 'googlefonts' );

			// Zip it up
			grunt.task.run( 'package' );
		}
	} );

	// Default task(s).
	grunt.registerTask( 'default', [ 'sass', 'csscomb:style', 'concat', 'uglify' ] );

	// Bump the version to the specified value; e.g., "grunt bumpto:patch"
	grunt.registerTask( 'bumpto', function( releaseType ) {
		if ( 'minor' !== releaseType && 'major' !== releaseType && 'patch' !== releaseType ) {
			grunt.fail.fatal( 'Please specify the bump type (e.g., "grunt bumpto:patch")' );
		} else {
			grunt.task.run( 'bump-only:' + releaseType );

			// Update the version numbers
			grunt.task.run( 'replace' );
		}
	} );

	// Prompt for the changelog
	grunt.registerTask( 'log', function( releaseType ) {
		var semver = require( 'semver' ),
			changelog,
			newVersion = semver.inc( grunt.config.get( 'pkg' ).version, releaseType),
			regex = new RegExp( '^## ' + newVersion, 'gm' ); // Match the version number (e.g., "# 1.2.3")

		if ( 'minor' !== releaseType && 'major' !== releaseType && 'patch' !== releaseType ) {
			grunt.log.writeln().fail( 'Please choose a valid version type (minor, major, or patch)' );
		} else {
			// Get the new version
			changelog = grunt.file.read( grunt.config.get( 'other' ).changelog );

			if ( changelog.match( regex ) ) {
				grunt.log.ok( 'v' + newVersion + ' changlelog entry found' );
			} else {
				grunt.fail.fatal( 'Please enter a changelog entry for v' + newVersion );
			}
		}
	} );

	// Package a new release
	grunt.registerTask( 'package', [
		'copy:build',
		'compress:build',
		'clean:build'
	] );

	// Process the icons YAML file
	grunt.registerTask( 'fontawesome', [
		'yaml:fontawesome',
		'json_massager:fontawesome',
		'json:fontawesome',
		'clean:assets'
	] );

	// Process the Google Fonts list
	grunt.registerTask( 'googlefonts', [
		'prompt:googlefonts',
		'curl:googlefonts',
		'json_massager:googlefonts',
		'shell:googlefonts',
		'copy:googlefonts',
		'clean:assets'
	] );
};