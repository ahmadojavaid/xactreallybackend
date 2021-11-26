<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Storage;

class Image extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'images';

    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'path',
        'type',
        'type_id'
    ];

    public function getPathAttribute($value)
    {
        return $value ? url('/storage/job_photos/'. $value) : null;
    }

    public static function upload($type, $images, $id)
    {
        foreach ($images as $image) {

            $name = rand(1111111111, 9999999999) .'_'. time() .'.'. $image->getClientOriginalExtension();
            $image->storeAs('public/job_photos', $name);

            $data[] = [
                'path' => $name,
                'type' => $type,
                'type_id' => $id
            ];
        }

        Image::insert($data);
    }

    public static function remove($image_ids)
    {
        Image::whereIn('_id', explode(',', $image_ids))->delete();
    }
}
