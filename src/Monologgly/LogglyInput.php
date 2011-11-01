<?php

namespace Monologgly;

interface LogglyInput
{
    const FORMAT_JSON = 'json';
    const FORMAT_TEXT = 'text';
    
    /**
     * Returns the input format.
     * 
     * @return string
     */
    public function getFormat();
}
