<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Developer extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'name',
        'avatar_url',
    ];

    /**
     * Get all PR reports for this developer.
     */
    public function prReports(): HasMany
    {
        return $this->hasMany(PrReport::class);
    }

    /**
     * Find or create a developer by username, updating name if provided.
     *
     * @param string $username The unique GitHub username
     * @param string|null $name The display name (updated on each call if different)
     * @param string|null $avatarUrl The avatar URL (updated if provided)
     * @return static
     */
    public static function syncByUsername(string $username, ?string $name = null, ?string $avatarUrl = null): static
    {
        $developer = static::firstOrCreate(
            ['username' => $username],
            ['name' => $name, 'avatar_url' => $avatarUrl]
        );

        // Update name if it changed
        $shouldUpdate = false;
        if ($name !== null && $developer->name !== $name) {
            $developer->name = $name;
            $shouldUpdate = true;
        }
        if ($avatarUrl !== null && $developer->avatar_url !== $avatarUrl) {
            $developer->avatar_url = $avatarUrl;
            $shouldUpdate = true;
        }

        if ($shouldUpdate) {
            $developer->save();
        }

        return $developer;
    }
}
