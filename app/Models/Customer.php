<?php

namespace App\Models;

use App\Observers\CustomerObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Phobiavr\PhoberLaravelCommon\Pageable\Pageable;
use Phobiavr\PhoberLaravelCommon\Traits\Authorable;

/**
 * @property Collection $contacts
 * @property LoyaltyCard|null $loyaltyCard
 */
#[ObservedBy([CustomerObserver::class])]
class Customer extends Model {
    use Pageable, Authorable, HasFactory;

    protected $with = ['contacts', 'loyaltyCard'];

    protected $fillable = [
        'birthday', 'first_name', 'last_name', 'gender', 'note'
    ];

    public function contacts(): HasMany {
        return $this->hasMany(Contact::class, 'customer_id', 'id');
    }

    public function loyaltyCard(): HasOne {
        return $this->hasOne(LoyaltyCard::class, 'id', 'id');
    }

    public function getFullNameAttribute(): string {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * days_until_birthday
     */
    public function getDaysUntilBirthdayAttribute(): int
    {
        $today = Carbon::today();

        $birthday = Carbon::parse($this->birthday)->year($today->year);

        if ($birthday->lt($today)) {
            $birthday->addYear();
        }

        return (int) $today->diffInDays($birthday);
    }
}
