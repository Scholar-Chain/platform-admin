<?php

namespace App\Models;

use App\Traits\FieldsType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Journal extends Model
{
    use HasFactory, FieldsType;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'price',
        'scope',
        'path',
        'thumbnail',
        'external_id',
        'is_active',
        'already_edit',
        'publish_months',
        'publisher_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'journal_id' => 'integer',
            'price' => 'integer',
            'scope' => 'array',
            'publish_months' => 'array',
            'publisher_id' => 'integer',
        ];
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }
}
