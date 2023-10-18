<?php

namespace App\Models\Crm\Contacts;

use App\Enums\DefaultStatus;
use App\Models\Business\Business;
use App\Models\Crm\Funnels\Funnel;
use App\Models\Crm\Funnels\ModelHasFunnelStage;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'crm_contacts';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contactable_type',
        'contactable_id',
        'user_id',
        'source_id',
        'status',
        'custom',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'custom' => 'array',
    ];

    /**
     * The business(es) that belong to the contact.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function business(): HasMany
    {
        return $this->hasMany(related: Business::class, foreignKey: 'contact_id');
    }

    /**
     * Get the business' funnel stages.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function funnelStages(): MorphMany
    {
        return $this->morphMany(related: ModelHasFunnelStage::class, name: 'model');
    }

    /**
     * Get all of the funnels for the contact.
     */
    public function funnels()
    {
        return $this->morphToMany(
            related: Funnel::class,
            name: 'funnelable',
            table: 'crm_funnelables',
            relatedPivotKey: 'funnel_id'
        );
    }

    /**
     * The roles that belongs to the contact.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(
            related: Role::class,
            table: 'crm_contact_has_roles',
            foreignPivotKey: 'contact_id',
            relatedPivotKey: 'contact_role_id'
        );
    }

    /**
     * The source of the contact.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(related: Source::class, foreignKey: 'source_id');
    }

    /**
     * The user that owns the contact.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(related: User::class, foreignKey: 'user_id');
    }

    /**
     * Get all of the owning contactable models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * SCOPES.
     *
     */

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

    public function getNameAttribute(): string
    {
        return $this->contactable->name;
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
