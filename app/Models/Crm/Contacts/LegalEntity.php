<?php

namespace App\Models\Crm\Contacts;

use App\Casts\DateCast;
use App\Casts\FloatCast;
use App\Traits\Crm\Contacts\Contactable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class LegalEntity extends Model implements HasMedia
{
    use HasFactory, Contactable;

    protected $table = 'crm_contact_legal_entities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'email',
        'additional_emails',
        'phones',
        'trade_name',
        'cnpj',
        'municipal_registration',
        'state_registration',
        'url',
        'sector',
        'num_employees',
        'anual_income'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'additional_emails' => 'array',
        'phones'            => 'array',
        'anual_income'      => FloatCast::class,
    ];

    /**
     * The individuals that belongs to the legal entity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function individuals(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Individual::class,
            table: 'crm_contact_individual_has_legal_entities',
            foreignPivotKey: 'legal_entity_id',
            relatedPivotKey: 'individual_id'
        )
            ->withPivot(columns: 'order');
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
}
