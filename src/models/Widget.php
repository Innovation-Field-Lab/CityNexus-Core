<?php

namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    protected $table = 'citynexus_widgets';

    public function getSettingAttribute()
    {
        return json_decode($this->settings);
    }
}
