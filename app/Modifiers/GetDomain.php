<?php

namespace App\Modifiers;

use Statamic\Modifiers\Modifier;

class GetDomain extends Modifier
{
    /**
     * Modify a value.
     *
     * @param mixed  $value    The value to be modified
     * @param array  $params   Any parameters used in the modifier
     * @param array  $context  Contextual values
     * @return mixed
     */
    public function index($value, $params, $context)
    {
        //find & remove protocol (http, ftp, etc.) and get $hostname
        if (strpos($value, "//") === false) {
            $hostname = explode('/', $value)[0];
        } else {
            $hostname = explode('/', $value)[2];
        }

        //find & remove port number
        $hostname = explode(':', $hostname)[0];

        //find & remove "?"
        $hostname = explode('?', $hostname)[0];

        //remove "www." from start of hostname
        if (strpos($hostname, "www.") === 0) {
            $hostname = explode('www.', $hostname)[1];
        }

        return $hostname;
    }
}
