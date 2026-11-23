<?php

namespace App\Models;

use App\Enums\InnovationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InnovationStatusHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'innovation_id',
        'changed_by_user_id',
        'from_status',
        'to_status',
        'remarks',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'from_status' => InnovationStatus::class,
            'to_status' => InnovationStatus::class,
            'created_at' => 'datetime',
        ];
    }

    public function innovation(): BelongsTo
    {
        return $this->belongsTo(Innovation::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
