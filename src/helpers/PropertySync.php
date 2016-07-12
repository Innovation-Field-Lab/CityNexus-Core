<?php


namespace CityNexus\CityNexus;


use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PropertySync
{
    /**
     *
     * Clean Address
     *
     * Take raw address upload and return a
     * lightly cleaned address for matching
     *
     * @param $address
     */
    private function cleanAddress($address)
    {

        if(is_array($address))
        {
            $pre = null;

            if(isset($address['house_number'])) $pre .= $address['house_number'];
            if(isset($address['street_name'])) $pre .= ' ' . $address['street_name'];
            if(isset($address['street_type'])) $pre .= ' ' . $address['house_number'];
            if(isset($address['unit'])) $pre .= ' ' . $address['unit'];
        }
        else
        {
            $pre = $address;
        }

        $post = $goodUrl = str_replace('.', '', strtoupper($pre));

        return $post;
    }
}