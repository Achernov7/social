<?php

namespace App\Components\SharedServices;


class getNameWithSurnameFromSearchingUser
{
    static function getNameSurname($searchingString):array|string
    {
        if (str_contains($searchingString, ' ') || str_contains($searchingString, '&nbsp;')) {
            $arrayOfSearches = explode(' ', $searchingString);
            $nameOrSuraname = [];
            foreach ($arrayOfSearches as $search){
                if (!str_contains($search, '&nbsp;')) {
                    array_push($nameOrSuraname, $search);
                    if (count($nameOrSuraname)>1){
                        break;
                    }
                } else {
                    $search = str_replace('&nbsp;', '', $search);           
                    if (strlen($search) > 0){
                        array_push($nameOrSuraname, $search);
                    }
    
                    if (count($nameOrSuraname)>1){
                        break;
                    }
                }
            }
    
            if (count($nameOrSuraname) == 1){
                return $nameOrSuraname[0];
            } else {
                return $nameOrSuraname;
            }
        } else {
            throw new \Exception('string doesn\'t contain spaces');
        }
    }

}
