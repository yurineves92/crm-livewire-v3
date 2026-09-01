<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deal extends Model
{
    use HasFactory;

    public const STAGE_PROSPECTING = 'prospecting';
    public const STAGE_PROPOSAL    = 'proposal';
    public const STAGE_NEGOTIATION = 'negotiation';
    public const STAGE_WON         = 'closed_won';
    public const STAGE_LOST        = 'closed_lost';

    /**
     * Estágios do funil e seus rótulos.
     *
     * @var array<string, string>
     */
    public const STAGES = [
        self::STAGE_PROSPECTING => 'Prospecção',
        self::STAGE_PROPOSAL    => 'Proposta',
        self::STAGE_NEGOTIATION => 'Negociação',
        self::STAGE_WON         => 'Ganho',
        self::STAGE_LOST        => 'Perdido',
    ];

    protected $fillable = ['customer_id', 'user_id', 'title', 'value', 'stage'];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStageLabelAttribute(): string
    {
        return self::STAGES[$this->stage] ?? $this->stage;
    }

    public function isOpen(): bool
    {
        return ! in_array($this->stage, [self::STAGE_WON, self::STAGE_LOST], true);
    }
}
