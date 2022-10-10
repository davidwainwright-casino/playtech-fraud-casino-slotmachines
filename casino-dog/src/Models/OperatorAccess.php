<?php

namespace Wainwright\CasinoDog\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Route;

class OperatorAccess extends Eloquent  {
    protected $table = 'wainwright_operator_access';
    protected $timestamp = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'operator_key',
        'operator_secret',
        'operator_access',
        'callback_url',
        'ownedBy',
        'active',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'last_used_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];


    public function user()
    {
        return $this->belongsTo('App\Models\User', 'ownedBy');
    }


}