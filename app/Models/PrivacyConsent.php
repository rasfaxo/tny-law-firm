<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivacyConsent extends Model
{
    protected $table = 'privacy_consents';

    protected $primaryKey = 'id_consent';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'id_user',
        'policy_version',
        'agreed_at',
    ];

    protected function casts(): array
    {
        return [
            'agreed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
