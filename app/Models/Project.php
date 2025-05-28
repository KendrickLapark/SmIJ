<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'created_by_user_id', 'last_used_at'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
