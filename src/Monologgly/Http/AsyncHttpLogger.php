<?php

namespace Monologgly\Http;

class AsyncHttpLogger extends HttpLogger
{
    public function write($message)
    {
        $fp = fsockopen($this->getTransport(), $this->port, $errno, $errstr, 30);
        
        if (FALSE === $fp) {
            throw new \Exception($errstr, $errno);
        }
        
        $request = "POST /inputs/".$this->input->getKey()." HTTP/1.1\r\n";
        $request.= "Host: ".$this->host."\r\n";
        $request.= "User-Agent: ".static::USER_AGENT."\r\n";
        $request.= "Content-Type: ".$this->getContentType()."\r\n";
        $request.= "Content-Length: ".strlen($message)."\r\n";
        $request.= "Connection: Close\r\n\r\n";
        $request.= $message;
        
        fwrite($fp, $request);
        fclose($fp);
        
        return TRUE;
    }
    
    public function getTransport()
    {
        switch ($this->port) {
            
            case static::PORT_HTTPS:
                return 'ssl://'.$this->host;
                break;
                
            case static::PORT_HTTP:
                return $this->host;
                break;
                
            default:
                throw new \LogicException('Allowed invalid port to be set.');
                break;
        }
    }
}
