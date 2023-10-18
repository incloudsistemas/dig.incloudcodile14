<?php

namespace App\Models\Crm\Funnels;

use App\Enums\Crm\Funnels\FunnelRole;
use App\Enums\DefaultStatus;
use App\Models\Business\Business;
use App\Models\Crm\Contacts\Contact;
use Illuminate\Contracts\Database\Eloquent\Builder;
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
        // 'slug',
        'description',
        'order',
        'status',
    ];

    /**
     * Get all of the contacts that are assigned this funnel.
     */
    public function contacts()
    {
        return $this->morphedByMany(
            related: Contact::class,
            name: 'funnelable',
            table: 'crm_funnelables',
            foreignPivotKey: 'funnel_id'
        );
    }

    /**
     * Get all of the business that are assigned this funnel.
     */
    public function business()
    {
        return $this->morphedByMany(
            related: Business::class,
            name: 'funnelable',
            table: 'crm_funnelables',
            foreignPivotKey: 'funnel_id'
        );
    }

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

    public function scopeByRoles(Builder $query, array $roles): Builder
    {
        return $query->whereIn('role', $roles);
    }

    public function scopeByStatuses(Builder $query, array $statuses = [1,]): Builder
    {
        return $query->whereIn('status', $statuses);
    }

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
