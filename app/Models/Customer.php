<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Phobiavr\PhoberLaravelCommon\Pageable\Pageable;

/**
 * @property Collection $contacts
 */
class Customer extends Model {
    use Pageable;

    protected $with = ['contacts'];

    protected $fillable = [
        'birthday', 'first_name', 'last_name'
    ];

    public function contacts(): HasMany {
        return $this->hasMany(Contact::class, 'customer_id', 'id');
    }

    public function getFullNameAttribute(): string {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getDaysUntilBirthdayAttribute() {
        $today = Carbon::today();

        $birthday = Carbon::parse($this->birthday);
        $birthday->year($today->year);

        if ($birthday->isPast()) {
            $birthday->addYear();
        }

        return $today->diffInDays($birthday);
    }
}
