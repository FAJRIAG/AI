<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Project extends Model
{
    protected $fillable = ['name', 'user_id', 'description', 'last_indexed_at'];

    protected $casts = [
        'last_indexed_at' => 'datetime'
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function sessions(): HasMany { return $this->hasMany(ChatSession::class); }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($project) {
            $project->sessions->each(function ($session) {
                $session->messages()->delete();
                $session->delete();
            });
        });
    }
}
