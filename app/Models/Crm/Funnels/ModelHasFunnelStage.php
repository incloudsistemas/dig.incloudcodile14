<?php

namespace App\Models\Crm\Funnels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ModelHasFunnelStage extends Model
{
    use HasFactory;

    protected $table = 'crm_model_has_funnel_stages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'funnel_id',
        'funnel_stage_id',
        'model_type',
        'model_id',
    ];

    /**
     * Get all of the owning stageable models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The stage of the model stage.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stage(): BelongsTo
    {
        return $this->belongsTo(related: FunnelStage::class, foreignKey: 'funnel_stage_id');
    }

    /**
     * The funnel of the model stage.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function funnel(): BelongsTo
    {
        return $this->belongsTo(related: Funnel::class, foreignKey: 'funnel_id');
    }

    /**
     * SCOPES.
     *
     */

    /**
     * MUTATORS.
     *
     */

    /**
     * CUSTOMS.
     *
     */

    public function getNameAttribute(): string
    {
        return $this->stage->name;
    }
}
