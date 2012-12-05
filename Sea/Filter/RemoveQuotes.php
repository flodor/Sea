<?php

class Sea_Filter_RemoveQuotes implements Zend_Filter_Interface 
{

    /**
     * Filtre les quotes et doubles quotes
     *
     * @param string $in
     * @return string
     */    
    public function filter($value)
    {
        $value = str_replace("'", "", $value);
        $value = str_replace('"', '', $value);
        
        return $value;
    }
}