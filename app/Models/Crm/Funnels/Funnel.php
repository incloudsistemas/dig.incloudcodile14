<?php

namespace App\Models\Crm\Funnels;

use App\Enums\Crm\Funnels\FunnelRole;
use App\Enums\DefaultStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Funnel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_funnels';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role',
        'name',
        'slug',
        'description',
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
        return $this->hasMany(related: ModelHasFunnelStage::class, foreignKey: 'funnel_id');
    }

    /**
     * The stages that belong to the funnel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stages(): HasMany
    {
        return $this->hasMany(related: FunnelStage::class, foreignKey: 'funnel_id');
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

    public function getDisplayRoleAttribute(): string
    {
        return isset($this->role)
            ? FunnelRole::getDescription(value: (int) $this->role)
            : null;
    }

    public function getDisplayStatusAttribute(): string
    {
        return DefaultStatus::getDescription(value: (int) $this->status);
    }

    public function getDisplayStatusColorAttribute(): string
    {
        return DefaultStatus::getColorByValue(status: (int) $this->status);
    }
}
