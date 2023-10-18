<?php

namespace App\Models\Crm\Contacts;

use App\Casts\DateCast;
use App\Enums\ProfileInfos\Gender;
use App\Traits\Crm\Contacts\Contactable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;

class Individual extends Model implements HasMedia
{
    use HasFactory, Contactable;

    protected $table = 'crm_contact_individuals';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        // 'slug',
        'email',
        'password',
        'additional_emails',
        'phones',
        'cpf',
        'rg',
        'gender',
        'birth_date',
        'occupation',
        'complement'
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
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'additional_emails' => 'array',
        'phones'            => 'array',
        'birth_date'        => DateCast::class
    ];

    /**
     * The legal entities that belongs to the contact.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function legalEntities(): BelongsToMany
    {
        return $this->belongsToMany(
            related: LegalEntity::class,
            table: 'crm_contact_individual_has_legal_entities',
            foreignPivotKey: 'individual_id',
            relatedPivotKey: 'legal_entity_id'
        )
            ->withPivot(columns: 'order');
    }

    /**
     * EVENT LISTENERS.
     *
     */

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (Self $individual): void {
            $individual->email = $individual->email . '//deleted_' . md5(uniqid());
            $individual->save();
        });
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

    public function getDisplayGenderAttribute(): ?string
    {
        return isset($this->gender)
            ? Gender::getDescription(value: $this->gender)
            : null;
    }

    public function getDisplayBirthDateAttribute(): ?string
    {
        // return $this->birth_date?->format('d/m/Y');
        return isset($this->birth_date)
            ? ConvertEnToPtBrDate(date: $this->birth_date)
            : null;
    }
}
