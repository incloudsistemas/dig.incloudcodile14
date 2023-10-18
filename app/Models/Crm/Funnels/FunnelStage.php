<?php

namespace App\Models\Crm\Funnels;

use App\Enums\DefaultStatus;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FunnelStage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_funnel_stages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'funnel_id',
        'name',
        // 'slug',
        'description',
        'business_probability',
        'order',
        'status',
    ];

    /**
     * The stages that belong to the funnel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function modelStages(): HasMany
    {
        return $this->hasMany(related: ModelHasFunnelStage::class, foreignKey: 'funnel_stage_id');
    }

    /**
     * The funnel of the stage.
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

    public function getDisplayStatusAttribute(): string
    {
        return DefaultStatus::getDescription(value: (int) $this->status);
    }

    public function getDisplayStatusColorAttribute(): string
    {
        return DefaultStatus::getColorByValue(status: (int) $this->status);
    }
}
