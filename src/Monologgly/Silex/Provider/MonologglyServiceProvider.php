<?php

namespace Monologgly\Silex\Provider;

use Monolog\Logger;
use Monologgly\Handler\LogglyHandler;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MonologglyServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['monolog'] = $app->share(function() use ($app) {
            $log = new Logger(isset($app['monolog.name']) ? $app['monolog.name'] : 'myapp');
            $app['monolog.configure']($log);
            return $log;
        });

        $app['monolog.configure'] = $app->protect(function($log) use ($app) {
            $log->pushHandler($app['monolog.handler']);
        });

        $app['monolog.handler'] = function() use ($app) {
            return new LogglyHandler(
                $app['loggly.input_token'],
                $app['loggly.input_type'],
                $app['monolog.level']
            );
        };

        if (!isset($app['monolog.level'])) {
            $app['monolog.level'] = function() {
                return Logger::DEBUG;
            };
        }

        if (isset($app['monolog.class_path'])) {
            $app['autoloader']->registerNamespace('Monolog', $app['monolog.class_path']);
        }

        $app->before(function() use ($app) {
            $app['monolog']->addInfo($app['request']->getMethod().' '.$app['request']->getRequestUri());
        });

        $app->error(function(\Exception $e) use ($app) {
            if ($e instanceof HttpException) {
                $app['monolog']->addWarning($e->getStatusCode().' '.$app['request']->getMethod().' '.$app['request']->getRequestUri());
            } else {
                $app['monolog']->addError($e->getMessage());
            }
        });
    }
}
