<?php

namespace Monologgly\Http;

use Monologgly\LogglyInput;

class HttpInput implements LogglyInput
{
    /**
     * @var string
     */
    protected $key;
    
    /**
     * @var string
     */
    protected $format;
    
    /**
     * Constructor.
     * 
     * @param string $key
     * @param string $format
     */
    public function __construct($key, $format = self::FORMAT_JSON)
    {
        $this->key = $key;
        $this->setFormat($format);
    }
    
	/**
     * @return string $key
     */
    public function getKey()
    {
        return $this->key;
    }

	/**
     * @return string $format
     */
    public function getFormat()
    {
        return $this->format;
    }

	/**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

	/**
     * @param string $format
     */
    public function setFormat($format)
    {
        if (!in_array($format, array(static::FORMAT_JSON, static::FORMAT_TEXT))) {
            throw new \InvalidArgumentException('Invalid Format');
        }
        
        $this->format = $format;
        
        return $this; 
    }
}
