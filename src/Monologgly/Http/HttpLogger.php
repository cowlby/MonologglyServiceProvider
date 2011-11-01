<?php

namespace Monologgly\Http;

use Monologgly\Loggly;
use Monologgly\LogglyInput;

abstract class HttpLogger implements Loggly
{
    const USER_AGENT = 'Monologgly/1.0 (+https://github.com/pradador/Monologgly)';
    
    const HOST_LOGGLY = 'logs.loggly.com';
    const HOST_AWS = 'ec2.logs.loggly.com';
    
    const PORT_HTTP = 80;
    const PORT_HTTPS = 443;
    
    const MEDIA_TYPE_JSON = 'application/json';
    const MEDIA_TYPE_TEXT = 'text/plain';
    
    /**
     * @var Monologgly\HttpInput
     */
    protected $input;
    
    /**
     * @var string
     */
    protected $host;
    
    /**
     * @var integer
     */
    protected $port;
    
    /**
     * Constructor.
     * 
     * @param Monologgly\HttpInput $input
     * @param string           $host
     * @param integer          $port
     */
    public function __construct(HttpInput $input, $host = self::HOST_LOGGLY, $port = self::PORT_HTTPS)
    {
        $this->setInput($input);
        $this->setHost($host);
        $this->setPort($port);
    }
    
    /**
     * @see Monologgly.Loggly::getInput()
     */
    public function getInput()
    {
        return $this->input;
    }
    
    public function setInput(HttpInput $input)
    {
        $this->input = $input;
        
        return $this;
    }
    
    public function setHost($host)
    {
        if (!in_array($host, array(self::HOST_LOGGLY, self::HOST_AWS))) {
            throw new \InvalidArgumentException('Invalid Host');
        }
        
        $this->host = $host;
        
        return $this;
    }
    
    public function setPort($port)
    {
        if (!in_array($port, array(self::PORT_HTTP, self::PORT_HTTPS))) {
            throw new \InvalidArgumentException('Invalid Port');
        }
        
        $this->port = $port;
        
        return $this;
    }
    
    public function getContentType()
    {
        switch ($this->input->getFormat()) {
            
            case LogglyInput::FORMAT_JSON:
                return self::MEDIA_TYPE_JSON;
                break;
                
            case LogglyInput::FORMAT_TEXT:
                return self::MEDIA_TYPE_TEXT;
                break;
                
            default:
                throw new \LogicException('Allowed invalid input format to be set.');
        } 
    }
}
