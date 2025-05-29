<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'title',
        'description',
        'start_datetime',
        'end_datetime',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    // Relación con Project
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Relación con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Duración en minutos (o la unidad que quieras)
    public function getDurationAttribute()
    {
        return $this->end_time && $this->start_time 
            ? $this->end_time->diffInMinutes($this->start_time)
            : 0;
    }
}
