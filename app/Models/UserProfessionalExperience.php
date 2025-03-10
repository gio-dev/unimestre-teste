<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfessionalExperience extends Model
{
    protected $fillable = [
        'user_id',
        'company',
        'position',
        'start_date',
        'end_date',
        'is_current',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
