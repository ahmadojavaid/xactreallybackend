<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class JobQuoteLine extends Eloquent
{

	protected $connection = 'mongodb';
    protected $collection = 'job_quote_lines';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'price',
    	'quantity',
    	'description',
        'image_url',
        'job_quote_id',

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

    public function JobQuoteMedia()
    {
    	return $this->hasMany('App\Models\JobQuoteMedia' , 'model_id')->where('model_type','JobQuoteLine');
    }
}
