const gulp = require( 'gulp' );

const composer = require( 'gulp-composer' );
const shell = require( 'gulp-shell' );
const merge = require( 'merge-stream' );
const replace = require( 'gulp-replace' );

gulp.task( 'build:css:admin', function () {
	return gulp
		.src( [ 'src/css/admin/*.css' ] )
		.pipe( gulp.dest( 'dist/admin/css/' ) );
} );

gulp.task( 'build:css:frontend', function () {
	return gulp
		.src( [ 'src/css/frontend/*.css' ] )
		.pipe( gulp.dest( 'dist/public/css/' ) );
} );

gulp.task(
	'build:css',
	gulp.parallel( 'build:css:admin', 'build:css:frontend' )
);

gulp.task( 'build:deps:composer:apiclient', function () {
	return merge(
		gulp
			.src(
				[
					'vendor/google/apiclient/src/Google/AccessToken/Revoke.php',
					'vendor/google/apiclient/src/Google/AuthHandler/AuthHandlerFactory.php',
					'vendor/google/apiclient/src/Google/AuthHandler/Guzzle6AuthHandler.php',
					'vendor/google/apiclient/src/Google/AuthHandler/Guzzle7AuthHandler.php',
					'vendor/google/apiclient/src/Google/Client.php',
					'vendor/google/apiclient/src/Google/Collection.php',
					'vendor/google/apiclient/src/Google/Exception.php',
					'vendor/google/apiclient/src/Google/Http/Batch.php',
					'vendor/google/apiclient/src/Google/Http/REST.php',
					'vendor/google/apiclient/src/Google/Service.php',
					'vendor/google/apiclient/src/Google/Service/Exception.php',
					'vendor/google/apiclient/src/Google/Task/Runner.php',
					'vendor/google/apiclient/src/Google/Utils/*',
					'!**/autoload.php',
					'!**/README*',
				],
				{ base: 'vendor/' }
			)
			.pipe( replace( /^<\?php/, '<?php\nnamespace Sgdd\\Vendor;' ) )
			.pipe( replace( /\nuse /g, '\nuse Sgdd\\Vendor\\' ) )
			.pipe(
				replace(
					/class_exists\('(?!\\)/g,
					"class_exists('\\\\Sgdd\\\\Vendor\\\\"
				)
			)
			.pipe(
				replace(
					/defined\('\\GuzzleHttp/g,
					"defined('\\Sgdd\\Vendor\\GuzzleHttp"
				)
			)
			.pipe( replace( / Iterator/g, ' \\Iterator' ) )
			.pipe( replace( / Countable/g, ' \\Countable' ) )
			.pipe( replace( / Exception/g, ' \\Exception' ) )
			.pipe( replace( / LogicException/g, ' \\LogicException' ) ),
		gulp
			.src( [ 'vendor/google/apiclient/src/Google/Model.php' ], {
				base: 'vendor/',
			} )
			.pipe( replace( /^<\?php/, '<?php\nnamespace Sgdd\\Vendor;' ) )
			.pipe( replace( / ArrayAccess/g, ' \\ArrayAccess' ) )
			.pipe( replace( 'stdClass', '\\stdClass' ) )
			.pipe( replace( 'ReflectionObject', '\\ReflectionObject' ) )
			.pipe( replace( 'ReflectionProperty', '\\ReflectionProperty' ) )
			.pipe(
				replace(
					'class_exists($this->$keyType)',
					"class_exists('\\\\Sgdd\\\\Vendor\\\\' . $this->$keyType)"
				)
			)
			.pipe(
				replace(
					'return $this->$keyType;',
					"return '\\\\Sgdd\\\\Vendor\\\\' . $this->$keyType;"
				)
			),
		gulp
			.src(
				[ 'vendor/google/apiclient/src/Google/Service/Resource.php' ],
				{
					base: 'vendor/',
				}
			)
			.pipe( replace( /^<\?php/, '<?php\nnamespace Sgdd\\Vendor;' ) )
			.pipe( replace( /\nuse /g, '\nuse Sgdd\\Vendor\\' ) )
			.pipe(
				replace(
					'public function call($name, $arguments, $expectedClass = null)\n  {',
					"public function call($name, $arguments, $expectedClass = null)\n  {\n    $expectedClass = '\\\\Sgdd\\\\Vendor\\\\' . $expectedClass;"
				)
			)
	).pipe( gulp.dest( 'dist/includes/vendor/' ) );
} );

gulp.task( 'build:deps:composer:apiclient-services', function () {
	return gulp
		.src(
			[
				'vendor/google/apiclient-services/src/Google/Service/Drive.php',
				'vendor/google/apiclient-services/src/Google/Service/Drive/Drive.php',
				'vendor/google/apiclient-services/src/Google/Service/Drive/DriveList.php',
				'vendor/google/apiclient-services/src/Google/Service/Drive/DriveFileImageMediaMetadata.php',
				'vendor/google/apiclient-services/src/Google/Service/Drive/DriveFile.php',
				'vendor/google/apiclient-services/src/Google/Service/Drive/FileList.php',
				'vendor/google/apiclient-services/src/Google/Service/Drive/Resource/*',
				'vendor/google/apiclient-services/src/Google/Service/Drive/Permission.php',
			],
			{ base: 'vendor/' }
		)
		.pipe( replace( /^<\?php/, '<?php\nnamespace Sgdd\\Vendor;' ) )
		.pipe( replace( /\nuse /g, '\nuse Sgdd\\Vendor\\' ) )
		.pipe( gulp.dest( 'dist/includes/vendor/' ) );
} );

gulp.task( 'build:deps:composer:licenses', function () {
	return gulp
		.src(
			[
				'vendor/google/apiclient-services/LICENSE',
				'vendor/google/apiclient/LICENSE',
				'vendor/google/auth/LICENSE',
				'vendor/guzzlehttp/guzzle/LICENSE',
				'vendor/guzzlehttp/promises/LICENSE',
				'vendor/guzzlehttp/psr7/LICENSE',
				'vendor/monolog/monolog/LICENSE',
				'vendor/psr/cache/LICENSE.txt',
				'vendor/psr/http-message/LICENSE',
				'vendor/psr/log/LICENSE',
			],
			{ base: 'vendor/' }
		)
		.pipe( gulp.dest( 'dist/includes/vendor/' ) );
} );

gulp.task( 'build:deps:composer:other', function () {
	return gulp
		.src(
			[
				'vendor/google/auth/src/Cache/Item.php',
				'vendor/google/auth/src/Cache/MemoryCacheItemPool.php',
				'vendor/google/auth/src/CacheTrait.php',
				'vendor/google/auth/src/FetchAuthTokenInterface.php',
				'vendor/google/auth/src/HttpHandler/Guzzle6HttpHandler.php',
				'vendor/google/auth/src/HttpHandler/Guzzle7HttpHandler.php',
				'vendor/google/auth/src/HttpHandler/HttpHandlerFactory.php',
				'vendor/google/auth/src/Middleware/ScopedAccessTokenMiddleware.php',
				'vendor/google/auth/src/OAuth2.php',
				'vendor/guzzlehttp/guzzle/src/Client.php',
				'vendor/guzzlehttp/guzzle/src/ClientInterface.php',
				'vendor/guzzlehttp/guzzle/src/ClientTrait.php',
				'vendor/guzzlehttp/guzzle/src/Handler/CurlFactory.php',
				'vendor/guzzlehttp/guzzle/src/Handler/CurlFactoryInterface.php',
				'vendor/guzzlehttp/guzzle/src/Handler/CurlHandler.php',
				'vendor/guzzlehttp/guzzle/src/Handler/CurlMultiHandler.php',
				'vendor/guzzlehttp/guzzle/src/Handler/EasyHandle.php',
				'vendor/guzzlehttp/guzzle/src/Handler/Proxy.php',
				'vendor/guzzlehttp/guzzle/src/Handler/StreamHandler.php',
				'vendor/guzzlehttp/guzzle/src/HandlerStack.php',
				'vendor/guzzlehttp/guzzle/src/Middleware.php',
				'vendor/guzzlehttp/guzzle/src/PrepareBodyMiddleware.php',
				'vendor/guzzlehttp/guzzle/src/RedirectMiddleware.php',
				'vendor/guzzlehttp/guzzle/src/RequestOptions.php',
				'vendor/guzzlehttp/guzzle/src/Utils.php',
				'vendor/guzzlehttp/guzzle/src/functions.php',
				'vendor/guzzlehttp/promises/src/Create.php',
				'vendor/guzzlehttp/promises/src/FulfilledPromise.php',
				'vendor/guzzlehttp/promises/src/Is.php',
				'vendor/guzzlehttp/promises/src/Promise.php',
				'vendor/guzzlehttp/promises/src/PromiseInterface.php',
				'vendor/guzzlehttp/promises/src/TaskQueue.php',
				'vendor/guzzlehttp/promises/src/TaskQueueInterface.php',
				'vendor/guzzlehttp/promises/src/Utils.php',
				'vendor/guzzlehttp/promises/src/functions.php',
				'vendor/guzzlehttp/psr7/src/MessageTrait.php',
				'vendor/guzzlehttp/psr7/src/Query.php',
				'vendor/guzzlehttp/psr7/src/Request.php',
				'vendor/guzzlehttp/psr7/src/Response.php',
				'vendor/guzzlehttp/psr7/src/Stream.php',
				'vendor/guzzlehttp/psr7/src/Uri.php',
				'vendor/guzzlehttp/psr7/src/UriResolver.php',
				'vendor/guzzlehttp/psr7/src/Utils.php',
				'vendor/guzzlehttp/psr7/src/functions.php',
				//'vendor/monolog/monolog/src/Monolog/Handler/Handler.php',
				'vendor/monolog/monolog/src/Monolog/Handler/AbstractHandler.php',
				'vendor/monolog/monolog/src/Monolog/Handler/AbstractProcessingHandler.php',
				'vendor/monolog/monolog/src/Monolog/Handler/HandlerInterface.php',
				'vendor/monolog/monolog/src/Monolog/Handler/StreamHandler.php',
				'vendor/monolog/monolog/src/Monolog/Formatter/FormatterInterface.php',
				'vendor/monolog/monolog/src/Monolog/Formatter/LineFormatter.php',
				'vendor/monolog/monolog/src/Monolog/Formatter/NormalizerFormatter.php',
				'vendor/monolog/monolog/src/Monolog/Logger.php',
				'vendor/monolog/monolog/src/Monolog/ResettableInterface.php',
				'vendor/monolog/monolog/src/Monolog/Utils.php',
				'vendor/psr/cache/src/CacheItemInterface.php',
				'vendor/psr/cache/src/CacheItemPoolInterface.php',
				'vendor/psr/http-client/src/ClientInterface.php',
				'vendor/psr/http-message/src/MessageInterface.php',
				'vendor/psr/http-message/src/RequestInterface.php',
				'vendor/psr/http-message/src/ResponseInterface.php',
				'vendor/psr/http-message/src/StreamInterface.php',
				'vendor/psr/http-message/src/UriInterface.php',
				'vendor/psr/log/Psr/Log/LoggerInterface.php',
			],
			{ base: 'vendor/' }
		)
		.pipe( replace( /\nnamespace /g, '\nnamespace Sgdd\\Vendor\\' ) )
		.pipe( replace( /\nuse /g, '\nuse Sgdd\\Vendor\\' ) )
		.pipe( replace( ' \\GuzzleHttp', ' \\Sgdd\\Vendor\\GuzzleHttp' ) )
		.pipe(
			replace(
				' \\Psr\\Http\\Client\\ClientInterface',
				' \\Sgdd\\Vendor\\Psr\\Http\\Client\\ClientInterface'
			)
		)
		.pipe(
			replace(
				/defined\('GuzzleHttp/g,
				"defined('\\Sgdd\\Vendor\\GuzzleHttp"
			)
		)
		.pipe( gulp.dest( 'dist/includes/vendor/' ) );
} );

gulp.task(
	'build:deps:composer',
	gulp.parallel(
		'build:deps:composer:apiclient',
		'build:deps:composer:apiclient-services',
		'build:deps:composer:licenses',
		'build:deps:composer:other'
	)
);

gulp.task( 'build:deps', gulp.parallel( 'build:deps:composer' ) );

gulp.task( 'build:gif', function () {
	return gulp
		.src( [ 'src/gif/loading-icon.gif' ] )
		.pipe( gulp.dest( 'dist/admin/' ) );
} );

gulp.task( 'build:js:admin', function () {
	return gulp
		.src( [ 'src/js/admin/*.js' ] )
		.pipe( gulp.dest( 'dist/admin/js/' ) );
} );

gulp.task( 'build:js:frontend', function () {
	return gulp
		.src( [ 'src/js/frontend/*.js' ] )
		.pipe( gulp.dest( 'dist/public/js/' ) );
} );

gulp.task( 'build:js', gulp.parallel( 'build:js:admin', 'build:js:frontend' ) );

gulp.task( 'build:php:admin', function () {
	return gulp
		.src( [ 'src/php/admin/**/*.php' ] )
		.pipe( gulp.dest( 'dist/admin/' ) );
} );

gulp.task( 'build:php:base', function () {
	return gulp.src( [ 'src/php/*.php' ] ).pipe( gulp.dest( 'dist/' ) );
} );

gulp.task( 'build:php:bundled', function () {
	return gulp
		.src( [ 'src/php/bundled/*.php' ] )
		.pipe( gulp.dest( 'dist/includes/' ) );
} );

gulp.task( 'build:php:frontend', function () {
	return gulp
		.src( [ 'src/php/frontend/**/*.php' ] )
		.pipe( gulp.dest( 'dist/public/' ) );
} );

gulp.task(
	'build:php',
	gulp.parallel(
		'build:php:admin',
		'build:php:base',
		'build:php:bundled',
		'build:php:frontend'
	)
);

gulp.task( 'build:png', function () {
	return gulp
		.src( [ 'src/png/icon.png' ] )
		.pipe( gulp.dest( 'dist/admin/' ) );
} );

gulp.task( 'build:txt', function () {
	return gulp.src( [ 'src/txt/*.txt' ] ).pipe( gulp.dest( 'dist/' ) );
} );

gulp.task(
	'build',
	gulp.parallel(
		'build:css',
		'build:deps',
		'build:gif',
		'build:js',
		'build:php',
		'build:png',
		'build:txt'
	)
);
