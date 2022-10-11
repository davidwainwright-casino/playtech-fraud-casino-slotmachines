<?php

use \Illuminate\Database\Eloquent\Model as Eloquent; //mysql & mariadb
#use Jenssegers\Mongodb\Eloquent\Model as Eloquent; //mongodb
#use JeffGreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;

class User extends \App\Models\User
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;
    protected $fillable = [
        'is_admin'
    ];

    public function is_admin() {
        if($this->user->is_admin === 1) {
            return true;
        } else {
            return false;
        }
    }

    public function operatoraccess(){
        return $this->belongsToMany('Wainwright\CasinoDog\Models\OperatorAccess', 'ownedBy');
    }
}