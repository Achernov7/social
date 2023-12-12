<?php

namespace App\Components\SharedServices;


class getTypeService
{
    static function getType($pattern)
    {
        $currenturl = url()->full();

        $match = preg_match($pattern, $currenturl, $type);

        if ($match && isset($type[1])) {
            return $type[1];
        } else {
            throw new \Exception('Invalid url');
        }
    }
}
