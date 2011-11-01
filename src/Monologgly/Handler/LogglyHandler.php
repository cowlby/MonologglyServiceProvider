<?php

namespace Monologgly\Handler;

use Monolog\Logger;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monologgly\Loggly;

class LogglyHandler extends AbstractProcessingHandler
{
    /**
     * @var Monologgly\Loggly
     */
    protected $loggly;
    
    /**
     * Constructor.
     * 
     * @param Monologgly\Loggly $loggly
     * @param integer           $level
     * @param boolean           $bubble
     */
    public function __construct(Loggly $loggly, $level = Loggly::DEBUG, $bubble = true)
    {
        $this->loggly = $loggly;
        
        parent::__construct($level, $bubble);
    }
    
    /**
     * @see Monolog\Handler.AbstractProcessingHandler::write()
     */
    protected function write(array $record)
    {
        $this->loggly->write($record['formatted']);
    }
    
    /**
     * Returns a JsonFormatter if the input type is JSON. Otherwise defaults
     * back to AbstractHandler behavior.
     * 
     * @see Monolog\Handler.AbstractHandler::getDefaultFormatter()
     */
    protected function getDefaultFormatter()
    {
        if ($this->loggly->getInput()->getFormat() === 'json') {
            return new JsonFormatter; 
        }
        
        return parent::getDefaultFormatter();
    }
}
