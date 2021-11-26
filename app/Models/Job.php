<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Job extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'jobs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'address',
        'primary_photo',
        'allow_bids',
        'fixed_price',
        'published'
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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'published' => 'boolean',
    ];

    public function setPrimaryPhotoAttribute($file)
    {
        if ($file) {
            $name = time() .'.'. $file->getClientOriginalExtension();
            $file->storeAs('public/job_photos/', $name);
            $this->attributes['primary_photo'] = $name;
        }
    }

    public function getPrimaryPhotoAttribute($value)
    {
        return $value ? url('/storage/job_photos/'. $value) : null;
    }

    public function elevations()
    {
        return $this->hasMany('App\Models\Elevation', 'job_id');
    }

    public function rooms()
    {
        return $this->hasMany('App\Models\Room', 'job_id');
    }

    public function jobAccess()
    {
        return $this->hasMany('App\Models\JobAccess' , 'job_id');
    }
}
