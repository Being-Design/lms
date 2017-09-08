// imports
var gulp = require('gulp');
var plumber = require('gulp-plumber');
var sass = require('gulp-sass');
var mainBowerFiles = require('main-bower-files');
var concat = require('gulp-concat');
var rename = require('gulp-rename');
var cssmin = require('gulp-clean-css');
var uglify = require('gulp-uglify');


// config
var path = {
	cssIn: 'src/scss',
	jsIn: 'src/js',
	cssOut: './css',
	jsOut: './js'
};


// default
gulp.task('default', [
	'bower',
	'js',
	'css'
	]);

// compile code
gulp.task('css', [
	'css:compile'
	]);
gulp.task('js', [
	'scripts:concat'
	]);

// compress code
gulp.task('concat', [
	'bower:concatScripts',
	'scripts:concat'
	]);
gulp.task('minify', [
	'css:minify',
	'scripts:minify'
	]);

// get libraries
gulp.task('bower', [
	'bower:getStyles',
	'bower:getScripts',
	'bower:concatScripts'
	]);


/* Custom Code */
gulp.task('css:compile', function() {
	return gulp.src(path.cssIn + '/main.scss')
	.pipe(plumber())
	.pipe( sass() )
	.pipe( gulp.dest(path.cssOut) );
});

gulp.task('css:minify', ['css:compile'], function() {
	return gulp.src([
		path.cssOut + '/*.css',
		'!'+path.cssOut+'/*.min.css'
		])
	.pipe( cssmin( {}, function(details) {
		console.log( 'original size:\t' + details.stats.originalSize);
		console.log( 'minified size:\t' + details.stats.minifiedSize);
		console.log( '% minified:\t' + (100 * (1 - details.stats.minifiedSize / details.stats.originalSize)) + '%');
	}) )
	.pipe( rename({
		suffix: '.min'
	}) )
	.pipe( gulp.dest(path.cssOut) );
})

gulp.task('scripts:concat', function() {
	return gulp.src([path.jsIn+'/**.js'])
	.pipe( concat('main.js') )
	.pipe( gulp.dest(path.jsOut) );
});

gulp.task('scripts:minify', ['scripts:concat'], function() {
	return gulp.src([
		path.jsOut+'/*.+(js)',
		'!'+path.jsOut+'/*.min.js'
		])
	.pipe( uglify( {}, function(details) {
		console.log( 'original size:\t' + details.stats.originalSize);
		console.log( 'minified size:\t' + details.stats.minifiedSize);
		console.log( '% minified:\t' + (100 * (1 - details.stats.minifiedSize / details.stats.originalSize)) + '%');
	}) )
	.pipe( rename({
		suffix: '.min'
	}) )
	.pipe( gulp.dest(path.jsOut) );
})
/* /Custom Code */


/** Bower Tasks **/
gulp.task('bower:getStyles', function() {
	return gulp.src(mainBowerFiles('**/*.+(css|eot|ttf|svg|woff|woff2)'), {
		base: 'bower_components'
	})
	.pipe(gulp.dest(path.cssOut+'/vendor'));
});

gulp.task('bower:getScripts', function() {
	return gulp.src( mainBowerFiles('**/*.js'), {
		base: 'bower_components'
	})
	.pipe( gulp.dest(path.jsOut+'/vendor') );
});

gulp.task('bower:concatScripts', ['bower:getScripts'], function() {
	return gulp.src( mainBowerFiles('**/*.js'), {
		base: 'bower_components'
	})
	.pipe( concat('bower-libraries.js') )
	.pipe( gulp.dest(path.jsOut) );
});

gulp.task('bower:jsmin', ['bower:concatScripts'], function() {
	return gulp.src(path.jsOut+'/bower-libraries.js')
	.pipe(plumber())
	.pipe( uglify() )
	.pipe( rename({
		suffix: '.min'
	}) )
	.pipe( gulp.dest(path.jsOut) );
});
/* /Bower Tasks */


/* Automation */
gulp.task('watch', function() {
	gulp.watch([path.cssIn+'/**/*.scss'], ['css']);
	gulp.watch([path.jsIn+'/**/*.js'], ['js']);
});
/* /Automation */
