<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Room extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'rooms';

    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dimensions',
        'notes',
        'job_id'
    ];

    public function images()
    {
        return $this->hasMany('App\Models\Image', 'type_id');
    }
}
