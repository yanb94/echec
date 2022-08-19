<?php

namespace App\Service;

use DateTime;

class RelativeDate
{

    public function turnDateToStringRelative(DateTime $date):string
    {
        $now = new DateTime('now');

        $diff = $now->diff($date);

        if ($diff->y > 0) {
            return $diff->y." ans";
        }
        if ($diff->m > 0) {
            return $diff->m." mois";
        }
        if ($diff->d > 0) {
            return $diff->d." jours";
        }
        if ($diff->h > 0) {
            return $diff->h."h";
        }
        if ($diff->i > 0) {
            return $diff->i."min";
        }
        if ($diff->s > 0) {
            return $diff->i."s";
        }
        
        return "";
    }
}
