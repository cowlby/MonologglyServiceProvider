<?php

namespace Monologgly\Handler;

use Monolog\Logger;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\AbstractProcessingHandler;

class LogglyHandler extends AbstractProcessingHandler
{
    const USER_AGENT = 'Monologgly/1.0 (+https://github.com/pradador/Monologgly)';
    
    const HOST_LOGGLY = 'logs.loggly.com';
    const HOST_EC2 = 'ec2.logs.loggly.com';
    
    const MEDIA_TYPE_JSON = 'application/json';
    const MEDIA_TYPE_TEXT = 'text/plain';
    
    protected $inputToken;
    protected $inputType;
    protected $host;
    protected $port;

    public function __construct($inputToken, $inputType = 'json', $level = Logger::DEBUG, $host = self::HOST_LOGGLY, $port = 443, $bubble = true)
    {
        $this->inputToken = $inputToken;
        $this->inputType = $inputType;
        $this->host = $host;
        $this->port = $port;
        
        parent::__construct($level, $bubble);
    }

    protected function write(array $record)
    {
        $host = $this->port === 443 ? 'ssl://'.$this->host : $this->host;
        $fp = fsockopen($host, $this->port, $errno, $errstr, 30);
        
        if (FALSE === $fp) {
            throw new \Exception($errstr, $errno);
        }
        
        $request = "POST /inputs/".$this->inputToken." HTTP/1.1\r\n";
        $request.= "Host: ".$this->host."\r\n";
        $request.= "User-Agent: ".self::USER_AGENT."\r\n";
        $request.= "Content-Type: ".$this->getContentType()."\r\n";
        $request.= "Content-Length: ".strlen($record['formatted'])."\r\n";
        $request.= "Connection: Close\r\n\r\n";
        $request.= $record['formatted'];
        
        fwrite($fp, $request);
        fclose($fp);
    }
    
    public function getContentType()
    {
        return $this->inputType == 'json' ? self::MEDIA_TYPE_JSON : self::MEDIA_TYPE_TEXT;
    }
    
    protected function getDefaultFormatter()
    {
        if ($this->inputType == 'json') {
            return new JsonFormatter; 
        }
        
        return parent::getDefaultFormatter();
    }
}
