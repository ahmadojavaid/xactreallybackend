<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Elevation extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'elevations';

    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'dimensions',
        'notes',
        'job_id'
    ];

    public function images()
    {
        return $this->hasMany('App\Models\Image', 'type_id');
    }
}
