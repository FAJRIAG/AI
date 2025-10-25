<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class ChatSession extends Model
{
    protected $fillable = ['project_id','title'];

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function messages(): HasMany { return $this->hasMany(Message::class)->orderBy('created_at'); }
}
