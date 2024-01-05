module.exports = function( grunt ) {
	// Project configuration.

	var phpFiles = [
		'**/*.php',
		'!bower_components/**',
		'!deploy/**',
		'!node_modules/**',
		'!vendor/**'
	];
	grunt.initConfig( {
		// Package
		pkg: grunt.file.readJSON( 'package.json' ),

		// PHP Code Sniffer
		phpcs: {
			application: {
				src: phpFiles
			},
			options: {
				bin: 'vendor/bin/phpcs',
				standard: 'phpcs.ruleset.xml',
				showSniffCodes: true
			}
		},
		
		// PHPLint
		phplint: {
			options: {
				phpArgs: {
					'-lf': null
				}
			},
			all: [ '**/*.php' ]
		},
		
		// Check WordPress version
		checkwpversion: {
			options: {
				readme: 'readme.txt',
				plugin: 'orbis-keychains.php',
			},
			check: {
				version1: 'plugin',
				version2: 'readme',
				compare: '=='
			},
			check2: {
				version1: 'plugin',
				version2: '<%= pkg.version %>',
				compare: '=='
			}
		},
		
		// MakePOT
		makepot: {
			target: {
				options: {
					cwd: '',
					domainPath: 'languages',
					type: 'wp-plugin'
				}
			}
		}
	} );

	grunt.loadNpmTasks( 'grunt-phpcs' );
	grunt.loadNpmTasks( 'grunt-phplint' );
	grunt.loadNpmTasks( 'grunt-checkwpversion' );
	grunt.loadNpmTasks( 'grunt-wp-i18n' );

	// Default task(s).
	grunt.registerTask( 'default', [ 'phplint', 'phpcs', 'checkwpversion' ] );
	grunt.registerTask( 'pot', [ 'makepot' ] );
};
