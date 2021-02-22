<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles()
    {   
        return $this->belongsToMany(\App\Models\Role::class,'role_users');
        //return $this->belongsToMany('App\Models\Role','role_users','user_id', 'role_id');
    }

    public function hasAccess(array $permissions)
    {    
        
        foreach($this->roles as $role){
            
            if($role->hasAccess($permissions)){  
                return true;
            }
        }
        
        return false;
    }

    public function inRole($roleSlug)
    {
        return $this->roles()->where('slug',$roleSlug)->count()==1;
    }
}
