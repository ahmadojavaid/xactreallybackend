<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class JobAccess extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'job_access';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'job_id',
        'user_id',
        'access_id',
        'accept_invitation',
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

    public function job()
    {
    	return $this->belongsTo('App\Models\Job' , 'job_id');
    }

    public function user()
    {
    	return $this->belongsTo('App\Models\User' , 'user_id');
    }

    public function access()
    {
    	return $this->belongsTo('App\Models\Access' , 'access_id');
    }
}
