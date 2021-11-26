<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class JobQuoteMedia extends Eloquent
{
	protected $connection = 'mongodb';
    protected $collection = 'job_quote_media';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'media_name',
        'media_type',
        'media_url',
        'model_type',
        'model_id',
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

    public function uploadMedia($model_type, $model_id, $file, $name)
    {	
    	$name = $name .'.'. $file->getClientOriginalExtension();
    	if($model_type == 'JobQuote')
    		$file->storeAs('public/job_quote_media/', $name);
    	else if($model_type == 'JobQuoteLine')
    		$file->storeAs('public/job_quote_media/', $name);

    	return $name;
    }	

    public function getMediaUrlAttribute($mediaUrl)
    {
    	return $mediaUrl ? url('/storage/job_quote_media/'. $mediaUrl) : null;
    }
}
