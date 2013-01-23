Monologgly, a Monolog Loggly handler
------------------------------------


MonologglyServiceProvider (Silex)
---------------------------------

Parameters
==========

- **loggly.input_key**: The input key for the Http Loggly Input. 
- **loggly.input_format**: The input format for the Loggly Input.
- **monolog.class_path** (optional): Path to where the Monolog library is located.
- **monologgly.class_path** (optional): Path to where the Monologgly library is located.
- **monolog.level** (optional): Level of logging defaults to DEBUG. Must be one
  of Logger::DEBUG, Logger::INFO, Logger::WARNING, Logger::ERROR. DEBUG will log
  everything, INFO will log everything except DEBUG, etc.
- **monolog.name** (optional): Name of the monolog channel, defaults to myapp.


Services
========

- **monolog**: The monolog logger instance.

    $app['monolog']->addDebug('Testing the Monolog logging.');


Registering
===========

Make sure you place a copy of Monolog and Monologgly in the vendor directory:

	$app->register(new Monologgly\Silex\Provider\MonologglyServiceProvider, array(
	    'loggly.input_key'      => '83e527d7-fad3-4d93-89da-0c2d8c0bcd6c',
	    'loggly.input_format'   => 'json',
	    'monolog.class_path'    => __DIR__.'/vendor/monolog/src',
	    'monologgly.class_path' => __DIR__.'/vendor/monologgly/src'
	));


Usage
=====

The MonologglyServiceProvider provides a monolog service. You can use it to add
log entries for any logging level through addDebug(), addInfo(), addWarning()
and addError(). Logs will be written to the specified Loggly Input as well as
to any of the additional handlers you define in 'monolog.handlers'. 

### Adding request logging ###

You can log requests easily by adding a before filter to your application. 

	$app->before(function () use ($app) {
        $app['monolog']->addDebug($app['request']->getMethod().' '.$app['request']->getRequestUri());
    });

### Adding error logging ###

To add error/exception logging by adding an error handler to your application
as such:

	$app->error(function(\Exception $e) use ($app) {
	    
	    if ($e instanceof HttpException) {
	        $app['monolog']->addInfo($e->getStatusCode().' '.$app['request']->getMethod().' '.$app['request']->getRequestUri());
	    } else {
		    $app['monolog']->addError($e->getMessage());
	    }
	});
	
A useful trick is to log at different levels depending on the HTTP status code.
For instance, you might want to treat 500 Server Errors as errors but 400
Client Errors should just be warnings.

	$app->error(function(\Exception $e) use ($app) {
	    
	    if ($e instanceof HttpException) {
	    
	    	$message = $e->getStatusCode().' '.$app['request']->getMethod().' '.$app['request']->getRequestUri();
	   		switch ($e->getStatusCode()) {
	   		    
	   		    case '404': // Not Found
	   		        $app['monolog']->addInfo($message);
	   		        break;
	   		        
	   		    case '400': // Bad Request
	   		        $app['monolog']->addWarn($message);
	   		        break;
	   		        
	   		    default:
	   		        $app['monolog']->addError($message);
	   		        break;
	   		}
	   		
	    } else {
    	    $app['monolog']->addError($e->getMessage());
	    }
	});
