<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\MapFile;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'otp_code',
        'otp_expire',
        'vip_level',
        'vip_expired_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expire' => 'datetime',
        'vip_expired_at' => 'datetime',
        'password' => 'hashed',
        'vip_level' => 'integer',
    ];

    public function mapFiles()
    {
        return $this->hasMany(MapFile::class, 'user_id');
    }

    public function getCurrentVipLevel(): int
    {
        $level = (int) ($this->vip_level ?? 0);

        if (in_array($level, [1, 2, 3])) {
            if ($this->vip_expired_at && now()->greaterThan($this->vip_expired_at)) {
                return 0;
            }
        }

        return $level;
    }

    public function getCurrentVipName(): string
    {
        return match ($this->getCurrentVipLevel()) {
            1 => 'VIP 1',
            2 => 'VIP 2',
            3 => 'VIP 3',
            default => 'FREE',
        };
    }

    public function getUploadLimitByType(string $type): int
    {
        $level = $this->getCurrentVipLevel();

        return match ($level) {
            0 => 1,
            1 => 3,
            2 => 9,
            3 => -1,
            default => 1,
        };
    }

    public function uploadedCountByType(string $type): int
    {
        return $this->mapFiles()->where('type', $type)->count();
    }

    public function canUploadType(string $type): bool
    {
        $limit = $this->getUploadLimitByType($type);

        if ($limit === -1) {
            return true;
        }

        return $this->uploadedCountByType($type) < $limit;
    }

    public function remainingUploadByType(string $type): ?int
    {
        $limit = $this->getUploadLimitByType($type);

        if ($limit === -1) {
            return null;
        }

        $used = $this->uploadedCountByType($type);

        return max(0, $limit - $used);
    }
}