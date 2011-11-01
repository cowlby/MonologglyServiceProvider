<?php

namespace Monologgly\Http;

class StrictHttpLogger extends HttpLogger
{
    public function write($message)
    {
        $ch = curl_init($this->getUrl());
        
        $headers = array(
        	'Content-Type: '.$this->getContentType(),
            'Content-Length: '.strlen($message)
        );
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_USERAGENT, static::USER_AGENT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
        
        $response = curl_exec($ch);
    
        if (0 !== $errno = curl_errno($ch)) {
            throw new \Exception(curl_error($ch), $errno);
        }
        
        return curl_getinfo($ch, CURLINFO_HTTP_CODE) === '200';
    }
    
    public function getUrl()
    {
        return sprintf('%s://%s/inputs/%s', $this->getScheme(), $this->host, $this->input->getKey());
    }
    
    public function getScheme()
    {
        switch ($this->port) {
            
            case static::PORT_HTTPS:
                return 'https';
                break;
                
            case static::PORT_HTTP:
                return 'http';
                break;
                
            default:
                throw new \LogicException('Allowed invalid port to be set.');
                break;
        }
    }
}
