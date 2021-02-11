<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Permissions\HasPermissionsTrait;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable;
    use HasPermissionsTrait;
    use HasApiTokens;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'password', 'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function complaints()
    {
        return $this->hasMany(\App\Models\Complaint::class, 'sender_id', 'id');
    }

    public function assigned()
    {
        return $this->hasMany(\App\Models\Assigned::class, 'executor_id', 'id');
    }

    public function mobileNotifications()
    {
        return $this->hasMany(\App\Models\MobileNotification::class, 'receiver_id', 'id');
    }

    public function attachFinished()
    {
        return $this->belongsTo(\App\Models\Assigned::class, 'attacher_id', 'id');
    }

    public function profile()
    {
        return $this->hasOne(\App\Models\Profile::class, 'user_id', 'id');
    }

}
