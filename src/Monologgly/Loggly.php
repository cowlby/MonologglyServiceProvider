<?php

namespace Monologgly;

interface Loggly
{
    /**
     * Returns the current LogglyInput.
     * 
     * @return Monologgly\LogglyInput
     */
    public function getInput();
    
    /**
     * Writes the message to Loggly.
     * 
     * @param string $message
     * @return boolean Whether or not the message was successfully written
     */
    public function write($message);
}
