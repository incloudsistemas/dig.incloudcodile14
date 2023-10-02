<?php

namespace App\Models\Cms;

use App\Casts\DateTimeCast;
use App\Enums\Cms\TestimonialRole;
use App\Traits\Cms\Postable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Testimonial extends Model implements HasMedia
{
    use HasFactory, Postable;

    protected $table = 'cms_testimonials';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role',
        'title',
        'slug',
        'subtitle',
        'customer_name',
        'occupation',
        'company',
        'excerpt',
        'body',
        'embed_video',
        'order',
        'featured',
        'publish_at',
        'expiration_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'featured'      => 'boolean',
        'publish_at'    => DateTimeCast::class,
        'expiration_at' => DateTimeCast::class,
    ];

    /**
     * EVENT LISTENERS.
     *
     */

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (Self $post): void {
            $post->slug = $post->slug . '//deleted_' . md5(uniqid());
            $post->save();
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

    public function getDisplayRoleAttribute(): string
    {
        return isset($this->role)
            ? TestimonialRole::getDescription(value: (int) $this->role)
            : null;
    }

    public function getCompanyLogoAttribute(): ?Media
    {
        $companyLogo = $this->getMedia('company-logo')
            ->first();

        return $companyLogo ?? null;
    }

    public function getDisplayCompanyLogoAttribute(): string
    {
        return isset($this->company_logo)
            ? $this->company_logo->getUrl()
            : PlaceholderImg(width: 1920, height: 1080);
    }
}
