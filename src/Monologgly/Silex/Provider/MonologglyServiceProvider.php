<?php

namespace Monologgly\Silex\Provider;

use Monolog\Logger;

use Monologgly\LogglyInput;
use Monologgly\HttpInput;
use Monologgly\AsyncHttpLogger;
use Monologgly\Handler\LogglyHandler;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MonologglyServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        // Register autoloaders.
        if (isset($app['monologgly.class_path'])) {
            $app['autoloader']->registerNamespace('Monologgly', $app['monologgly.class_path']);
        }

        if (isset($app['monolog.class_path'])) {
            $app['autoloader']->registerNamespace('Monolog', $app['monolog.class_path']);
        }
        
        // Set default options.
        if (!isset($app['monolog.level'])) {
            $app['monolog.level'] = Logger::DEBUG;
        }
        
        if (!isset($app['loggly.input_format'])) {
            $app['loggly.input_format'] = LogglyInput::FORMAT_JSON;
        }
        
        // Services.
        $app['monologgly'] = $app->share(function() use ($app) {
            return new AsyncHttpLogger($app['monologgly.input']);
        });
        
        $app['monologgly.input'] = $app->share(function() use ($app) {
            return new HttpInput($app['loggly.input_key'], $app['loggly.input_format']);
        });

        $app['monologgly.handler'] = $app->share(function() use ($app) {
            return new LogglyHandler($app['monologgly'], $app['monolog.level']);
        });
        
        $app['monolog'] = $app->share(function() use ($app) {
            $log = new Logger(isset($app['monolog.name']) ? $app['monolog.name'] : 'myapp');
            $app['monolog.configure']($log);
            return $log;
        });

        $app['monolog.configure'] = $app->protect(function(Logger $log) use ($app) {
            
            if (isset($app['monolog.handlers'])) {
                foreach ($app['monolog.handlers'] as $handler) {
                    $log->pushHandler($handler);
                }
            }
            
            $log->pushHandler($app['monologgly.handler']);
        });
    }
}
