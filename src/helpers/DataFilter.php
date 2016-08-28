<?php


namespace CityNexus\CityNexus;

use CityNexus\CityNexus\Http\TablerController;
use Maatwebsite\Excel\Facades\Excel;

class DataFilter
{

    public function process($data, $filters)
    {
        foreach($filters as $i)
        {
            $data = $this->$i->function($data, $i->options);
        }

        return $data;
    }

    private function searchReplace($data, $options)
    {
        $return = str_replace($options->needle, $options->replace, $data);
        return $return;
    }
}