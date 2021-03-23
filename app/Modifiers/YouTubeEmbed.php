<?php

namespace App\Modifiers;

use Statamic\Modifiers\Modifier;

class YouTubeEmbed extends Modifier
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
        preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $value, $matches);

        $embed = '<iframe width="640" height="360" src="https://www.youtube.com/embed/' . $matches[0] . '?feature=oembed" frameborder="0" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';

        return $embed;
    }
}
