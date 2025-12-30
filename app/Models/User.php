<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'role_id',
        'first_name',
        'middle_name',
        'last_name',
        'birth_date',
        'avatar',
        'avatar_focus',
        'bio',
        'is_profile_public',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // helper
    public function isAdmin(): bool
    {
        return $this->role?->name === 'admin';
    }

    public function isMentor(): bool
    {
        return $this->role?->name === 'mentor';
    }

    public function isMember(): bool
    {
        return $this->role?->name === 'member';
    }

    // Helper untuk display name
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?: $this->username;
    }

    // Helper untuk cek apakah profile sudah lengkap
    public function hasCompletedProfile(): bool
    {
        return !is_null($this->name);
    }

    /**
     * Helper untuk cek apakah onboarding sudah selesai
     */
    public function hasCompletedOnboarding(): bool
    {
        return !is_null($this->first_name) &&
            !is_null($this->last_name) &&
            !is_null($this->birth_date) &&
            !is_null($this->name);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'is_profile_public' => 'boolean',
            'avatar_focus' => 'array',
        ];
    }
    public function getFullNameAttribute(): string
    {
        return trim(collect([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ])->filter()->implode(' '));
    }

    /**
     * Sync the main 'name' field from first_name, middle_name, last_name
     * Used by observer and manual sync
     */
    public function syncNameFromParts(): void
    {
        $this->name = $this->full_name;
    }

    /**
     * Relasi ke role requests
     */
    public function roleRequests()
    {
        return $this->hasMany(RoleRequest::class);
    }

    /**
     * Get pending role request
     */
    public function pendingRoleRequest()
    {
        return $this->roleRequests()->pending()->latest()->first();
    }

    /**
     * Check if user has pending role request
     */
    public function hasPendingRoleRequest(): bool
    {
        return $this->roleRequests()->pending()->exists();
    }

    /**
     * Calculate age from birth_date
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->birth_date) {
            return null;
        }

        return \Carbon\Carbon::parse($this->birth_date)->age;
    }

    /**
     * Relasi ke activity privacies
     */
    public function activityPrivacies()
    {
        return $this->hasMany(UserActivityPrivacy::class);
    }

    /**
     * Get privacy setting for specific activity type
     */
    public function getActivityPrivacy(string $activityType): bool
    {
        $privacy = $this->activityPrivacies()->ofType($activityType)->first();
        return $privacy ? $privacy->is_public : true; // Default to public
    }

    /**
     * Set privacy setting for specific activity type
     */
    public function setActivityPrivacy(string $activityType, bool $isPublic): void
    {
        $this->activityPrivacies()->updateOrCreate(
            ['activity_type' => $activityType],
            ['is_public' => $isPublic]
        );
    }

    /**
     * Check if profile is public
     */
    public function isProfilePublic(): bool
    {
        return $this->is_profile_public ?? false;
    }

    /**
     * Check if profile is private
     */
    public function isProfilePrivate(): bool
    {
        return !$this->isProfilePublic();
    }

    /**
     * Events the user has participated in.
     */
    public function participatedEvents()
    {
        return $this->belongsToMany(Event::class, 'event_participants')
            ->withPivot(['registration_status', 'attendance_status', 'completion_status', 'joined_at'])
            ->withTimestamps();
    }

    /**
     * Events the user is registered for (active registrations).
     */
    public function registeredEvents()
    {
        return $this->participatedEvents()
            ->wherePivot('registration_status', 'registered');
    }

    /**
     * Events the user has attended (completed).
     */
    public function attendedEvents()
    {
        return $this->participatedEvents()
            ->wherePivot('attendance_status', 'present');
    }

    /**
     * Events the user is mentoring.
     */
    public function mentoringEvents()
    {
        return $this->belongsToMany(Event::class, 'event_mentors')
            ->withPivot(['role', 'goal_title', 'goal_detail', 'goal_status'])
            ->withTimestamps();
    }

    /**
     * Reviews/feedback the user has submitted.
     */
    public function eventFeedback()
    {
        return $this->hasMany(EventFeedback::class, 'from_user_id');
    }

    /**
     * Check if user has reviewed a specific event.
     */
    public function hasReviewedEvent(Event $event): bool
    {
        return $this->eventFeedback()
            ->where('event_id', $event->id)
            ->whereNull('to_user_id')
            ->exists();
    }

    /**
     * Check if user is registered for a specific event.
     */
    public function isRegisteredForEvent(Event $event): bool
    {
        return $this->registeredEvents()
            ->where('events.id', $event->id)
            ->exists();
    }

    /**
     * User's tech skills.
     */
    public function skills()
    {
        return $this->hasMany(UserSkill::class)->orderBy('level', 'desc');
    }

    /**
     * Get avatar URL with cache-busting version.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->avatar) {
            return null;
        }

        $version = $this->updated_at ? $this->updated_at->timestamp : time();
        return asset('storage/' . $this->avatar) . '?v=' . $version;
    }

    /**
     * Get avatar focus with defaults.
     */
    public function getAvatarFocusWithDefaults(): array
    {
        $default = ['x' => 0.5, 'y' => 0.5, 'zoom' => 1.0];
        return array_merge($default, $this->avatar_focus ?? []);
    }

    /**
     * Get CSS style string for avatar display.
     */
    public function getAvatarStyleAttribute(): string
    {
        $focus = $this->getAvatarFocusWithDefaults();
        $posX = ($focus['x'] ?? 0.5) * 100;
        $posY = ($focus['y'] ?? 0.5) * 100;
        $zoom = $focus['zoom'] ?? 1.0;

        return "object-position: {$posX}% {$posY}%; transform: scale({$zoom}); transform-origin: {$posX}% {$posY}%;";
    }

    /**
     * User's portfolio items.
     */
    public function portfolios()
    {
        return $this->hasMany(Portfolio::class)->latest();
    }

    /**
     * User's published portfolios.
     */
    public function publishedPortfolios()
    {
        return $this->portfolios()->published();
    }

    /**
     * User's course enrollments.
     */
    public function userCourses()
    {
        return $this->hasMany(UserCourse::class)->latest();
    }

    /**
     * User's published courses.
     */
    public function publishedCourses()
    {
        return $this->userCourses()->published();
    }
}
