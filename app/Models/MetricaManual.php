<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetricaManual extends Model
{
    use HasFactory;

    protected $table = 'metrica_manuals';

    protected $fillable = [
        'user_id',
        'data_inicio',
        'data_fim',
        'tipo',
        'valor',
        'observacao',
    ];

    protected $casts = [
        'data_inicio' => 'date:Y-m-d',
        'data_fim'    => 'date:Y-m-d',
        'valor'       => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}