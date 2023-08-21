<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Casts\DateCast;
use App\Enums\ProfileInfos\EducationalLevel;
use App\Enums\ProfileInfos\Gender;
use App\Enums\ProfileInfos\MaritalStatus;
use App\Enums\UserStatus;
use App\Models\Address;
use App\Services\Permissions\RoleService;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

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
     * The attributes that are timestamps.
     *
     * @var array
     */
    protected $dates = ['birth_date'];

    /**
     * Get the user's addresses.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->status != 1) {
            // auth()->logout();
            return false;
        }

        return true;
    }

    /**
     * SCOPES.
     *
     */

    public function scopeByAuthUserRoles(Builder $query, User $user): Builder
    {
        $rolesToAvoid = RoleService::getListOfRolesToAvoidByAuthUserRoles($user);

        return $query->whereHas('roles', function ($query) use ($rolesToAvoid) {
            $query->whereNotIn('id', $rolesToAvoid);
        });
    }

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
        return isset($this->gender) ? Gender::getDescription($this->gender) : null;
    }

    public function getDisplayBirthDateAttribute(): ?string
    {
        // return $this->birth_date?->format('d/m/Y');
        return isset($this->birth_date) ? ConvertEnToPtBrDate($this->birth_date) : null;
    }

    public function getDisplayMaritalStatusAttribute(): ?string
    {
        return isset($this->marital_status) ? MaritalStatus::getDescription((int) $this->marital_status) : null;
    }

    public function getDisplayEducationalLevelAttribute(): ?string
    {
        return isset($this->educational_level) ? EducationalLevel::getDescription((int) $this->educational_level) : null;
    }

    public function getDisplayStatusAttribute(): string
    {
        return UserStatus::getDescription((int) $this->status);
    }

    public function getDisplayStatusColorAttribute(): string
    {
        return UserStatus::getColorByValue((int) $this->status);
    }
}
