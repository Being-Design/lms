// Include Gulp
var gulp = require( 'gulp' );

// Include plugins
var plugins = require( "gulp-load-plugins" ) ( {
	pattern: ['gulp-*', 'gulp.*', 'main-bower-files'],
	replaceString: /\bgulp[\-.]/
});

gulp.task( 'js', function() {
	gulp.src( plugins.mainBowerFiles() )
		.pipe( plugins.filter( '**/*.js' ) )
		.pipe( plugins.uglify() )
		.pipe( gulp.dest( 'js/vendor/' ) );
});

gulp.task( 'css', function() {
	gulp.src( plugins.mainBowerFiles() )
		.pipe( plugins.filter( '**/*.css' ) )
		.pipe( plugins.minify() )
		.pipe( gulp.dest( 'css/vendor/' ) );
});
