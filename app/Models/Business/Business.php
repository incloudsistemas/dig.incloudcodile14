<?php

namespace App\Models\Business;

use App\Casts\DateTimeCast;
use App\Casts\FloatCast;
use App\Enums\Business\BusinessRole;
use App\Enums\Business\PaymentMethod;
use App\Models\Address;
use App\Models\Crm\Contacts\Contact;
use App\Models\Crm\Funnels\Funnel;
use App\Models\Crm\Funnels\ModelHasFunnelStage;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'business';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'contact_id',
        'role',
        'requires_shipping',
        'shipping_cost',
        'price',
        'cost',
        'discount',
        'payment_method',
        'num_installments',
        'description',
        'business_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'requires_shipping' => 'boolean',
        'shipping_cost'     => FloatCast::class,
        'price'             => FloatCast::class,
        'cost'              => FloatCast::class,
        'discount'          => FloatCast::class,
        'business_at'       => DateTimeCast::class,
    ];

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
     * Get all of the funnels for the business.
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
     * The traded items that belong to the business.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tradedItems(): HasMany
    {
        return $this->hasMany(related: TradedItem::class, foreignKey: 'business_id');
    }

    /**
     * The contact that owns the business.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(related: Contact::class, foreignKey: 'contact_id');
    }

    /**
     * The user that owns the business.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(related: User::class, foreignKey: 'user_id');
    }

    /**
     * Get the user's addresses.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(related: Address::class, name: 'addressable');
    }

    /**
     * SCOPES.
     *
     */

    public function scopeByRoles(Builder $query, array $roles): Builder
    {
        return $query->whereIn('role', $roles);
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
            ? BusinessRole::getDescription(value: (int) $this->role)
            : null;
    }

    public function getDisplayShippingCostAttribute(): string
    {
        return $this->shipping_cost ? number_format($this->shipping_cost, 2, ',', '.') : '0,00';
    }

    public function getDisplayPriceAttribute(): string
    {
        return $this->price ? number_format($this->price, 2, ',', '.') : '0,00';
    }

    public function getDisplayCostAttribute(): string
    {
        return $this->cost ? number_format($this->cost, 2, ',', '.') : '0,00';
    }

    public function getDisplayDiscountAttribute(): string
    {
        return $this->discount ? number_format($this->discount, 2, ',', '.') : '0,00';
    }

    public function getDisplayPaymentMethodAttribute(): ?string
    {
        return isset($this->payment_method)
            ? PaymentMethod::getDescription(value: (int) $this->payment_method)
            : null;
    }

    public function getDisplayNumInstallmentsAttribute(): string
    {
        return (int) $this->num_installments === 0 ? 'À vista' : $this->num_installments . 'x';
    }
}
