<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    protected $fillable = [
        'name', 'vlan_id', 'unit_number'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

}
