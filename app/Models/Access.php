<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Access extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'access';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function jobAccess()
    {
    	return $this->hasMany('App\Models\jobAccess' , 'access_id');
    }
}
