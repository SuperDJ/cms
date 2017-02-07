var gulp = require( 'gulp' ),
	util = require( 'gulp-util' ),
	concat = require( 'gulp-concat' ),
	jshint = require( 'gulp-jshint' ),
	sourcemaps = require( 'gulp-sourcemaps' ),
	uglify = require( 'gulp-uglify' ),
	watch = require( 'gulp-watch' ),
	plumber = require( 'gulp-plumber' ),
	sass = require( 'gulp-sass' ),
	autoprefixer = require( 'gulp-autoprefixer' ),
	zip = require( 'gulp-zip' );

// Compress app.js
gulp.task( 'app', function () {
	gulp.src( 'js/app/*.js' )
		.pipe( plumber() )
		.pipe( jshint() )
		.pipe( sourcemaps.init() )
		.pipe( concat( 'app.min.js' ) )
		.pipe( uglify() )
		.pipe( sourcemaps.write( 'sources' ) )
		.pipe( gulp.dest( 'js' ) );
} );

// Compress vendor.js
gulp.task( 'vendor', function () {
	gulp.src( ['js/vendor/*.js'] )
		.pipe( plumber() )
		.pipe( jshint() )
		.pipe( sourcemaps.init() )
		.pipe( concat( 'vendor.min.js' ) )
		.pipe( uglify() )
		.pipe( sourcemaps.write( 'sources' ) )
		.pipe( gulp.dest( 'js' ) );
} );

// Compress .scss files
gulp.task( 'sass', function () {
	gulp.src( './stylesheets/scss/*.scss' )
		.pipe( sourcemaps.init() )
		.pipe( sass( {
			errLogToConsole: true,
			outputStyle: 'compressed'
		} ).on( 'error', sass.logError ) )
		.pipe( autoprefixer() )
		.pipe( sourcemaps.write( './' ) )
		.pipe( gulp.dest( 'stylesheets' ) );
} );

// SCSS Zip all required files
gulp.task( 'scss-zip', function () {
	gulp.src( ['js/*', 'js/*/**.js', 'stylesheets/*', 'stylesheets/*/**.scss', 'stylesheets/*/*/**.scss', 'stylesheets/*/*/*/**.ttf', 'bower.json', 'index.php', 'includes/*', 'gulpfile.js', 'package.json'], {base: '.'} )
		.pipe( zip( 'smaterial-scss.zip' ) )
		.pipe( gulp.dest( './' ) );
} );

// CSS Zip all required files
gulp.task( 'css-zip', function () {
	gulp.src( ['js/app.min.js', 'js/vendor.min.js', 'stylesheets/smaterial.css', 'stylesheets/*/*/*/**.ttf', 'index.php', 'bower.json', 'includes/*'], {base: '.'} )
		.pipe( zip( 'smaterial-css.zip' ) )
		.pipe( gulp.dest( './' ) );
} );

// Zip all required files
gulp.task( 'zip', ['scss-zip', 'css-zip'] );

// Watch files for changes
gulp.task( 'watch', function () {
	gulp.watch( 'js/app/*.js', ['app'] );
	gulp.watch( 'js/vendor/*.js', ['vendor'] );
	gulp.watch( 'stylesheets/scss/*.scss', ['sass'] );
	gulp.watch( 'stylesheets/scss/**/*.scss', ['sass'] );
} );

gulp.task( 'default', ['watch'] );