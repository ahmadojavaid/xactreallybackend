<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Support\Str;

class User extends Eloquent implements AuthenticatableContract
{
    use Authenticatable;
    
    protected $connection = 'mongodb';
    protected $collection = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'profile_pic',
        'first_name',
        'last_name',
        'email',
        'email_verified',
        'phone',
        'phone_verified',
        'country',
        'role',
        'password',
        'auth_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified' => 'boolean',
        'phone_verified' => 'boolean',
    ];

    public function setProfilePicAttribute($file)
    {
        if ($file) {
            $name = time() .'.'. $file->getClientOriginalExtension();
            $file->storeAs('public/profile_pics/', $name);
            $this->attributes['profile_pic'] = $name;
        }
    }

    public function getProfilePicAttribute($value)
    {
        return $value ? url('/storage/profile_pics/'. $value) : null;
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function generateAuthToken() {

        do { $token = Str::random(60); } while (self::where('auth_token', $token)->exists());

        $this->auth_token = $token;
        $this->save();
    }
}
