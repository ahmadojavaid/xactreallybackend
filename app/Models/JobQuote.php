<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class JobQuote extends Eloquent
{
	protected $connection = 'mongodb';
    protected $collection = 'job_quotes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'name',
    	'description',
    	'quantity',
    	'tax',
    	'recoverable_cost',
    	'o_and_p',
    	'total',
    	'status_remove_replace',
        'image_url',
        'user_id',
        'job_id',
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

    public function getImageUrlAttribute($image_url)
    {
        return ($image_url)?asset($image_url):null;
    }

    public function user()
    {
    	return $this->belongsTo('App\Models\user' , 'user_id');
    }

    public function job()
    {
    	return $this->belongsTo('App\Models\Job' , 'job_id');
    }

    public function JobQuoteMedia()
    {
    	return $this->hasMany('App\Models\JobQuoteMedia' , 'model_id')->where('model_type','JobQuote');
    }

    public function JobQuoteLines($value='')
    {
    	return $this->hasMany('App\Models\JobQuoteLine' , 'job_quote_id');
    }
}
