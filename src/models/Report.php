<?php

namespace CityNexus\CityNexus;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'citynexus_reports';
    protected $fillable = ['name', 'settings', 'access'];

    public function getSettingAttribute()
    {
        return json_decode($this->settings);
    }
}
