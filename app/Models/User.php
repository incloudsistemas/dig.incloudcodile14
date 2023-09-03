<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Casts\DateCast;
use App\Enums\ProfileInfos\EducationalLevel;
use App\Enums\ProfileInfos\Gender;
use App\Enums\ProfileInfos\MaritalStatus;
use App\Enums\UserStatus;
use App\Models\Address;
use App\Models\Cms\Post;
use App\Services\Permissions\RoleService;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements FilamentUser, HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'additional_emails',
        'phones',
        'cpf',
        'rg',
        'gender',
        'birth_date',
        'marital_status',
        'educational_level',
        'nationality',
        'citizenship',
        'complement',
        'status',
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
        'password' => 'hashed',
        'additional_emails' => 'array',
        'phones' => 'array',
        'birth_date' => DateCast::class
    ];

    /**
     * The cms posts that belong to the owner/user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cmsPosts(): HasMany
    {
        return $this->hasMany(related: Post::class);
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

    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->status != 1) {
            // auth()->logout();
            return false;
        }

        return true;
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Manipulations::FIT_CROP, 150, 150)
            ->nonQueued();
    }

    /**
     * SCOPES.
     *
     */

    public function scopeByAuthUserRoles(Builder $query, User $user): Builder
    {
        $rolesToAvoid = RoleService::getArrayOfRolesToAvoidByAuthUserRoles($user);

        return $query->whereHas('roles', function (Builder $query) use ($rolesToAvoid): Builder {
            return $query->whereNotIn('id', $rolesToAvoid);
        });
    }

    public function scopeWhereHasRolesAvoidingClients(Builder $query): Builder
    {
        $rolesToAvoid = [2,]; // Client/Cliente

        return $query->whereHas('roles', function (Builder $query) use ($rolesToAvoid): Builder {
            return $query->whereNotIn('id', $rolesToAvoid);
        });
    }

    public function scopeByStatuses(Builder $query, array $statuses = [1,]): Builder
    {
        return $query->whereHasRolesAvoidingClients()
            ->whereIn('status', $statuses);
    }

    /**
     * MUTATORS.
     *
     */

    /**
     * CUSTOMS.
     *
     */

    public function getDisplayMainPhoneAttribute(): ?string
    {
        return $this->phones[0]['number'] ?? null;
    }

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

    public function getDisplayMaritalStatusAttribute(): ?string
    {
        return isset($this->marital_status)
            ? MaritalStatus::getDescription(value: (int) $this->marital_status)
            : null;
    }

    public function getDisplayEducationalLevelAttribute(): ?string
    {
        return isset($this->educational_level)
            ? EducationalLevel::getDescription(value: (int) $this->educational_level)
            : null;
    }

    public function getDisplayStatusAttribute(): string
    {
        return UserStatus::getDescription(value: (int) $this->status);
    }

    public function getDisplayStatusColorAttribute(): string
    {
        return UserStatus::getColorByValue(status: (int) $this->status);
    }
}
