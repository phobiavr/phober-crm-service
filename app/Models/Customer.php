<?php

namespace App\Models;

use App\Observers\CustomerObserver;
use Carbon\Carbon;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Phobiavr\PhoberLaravelCommon\Pageable\Pageable;
use Phobiavr\PhoberLaravelCommon\Traits\Authorable;

/**
 * @property int $id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string $birthday
 * @property int|null $birthday_month_day
 * @property string|null $gender
 * @property int $discount
 * @property int $balance
 * @property string $status
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $full_name
 * @property-read int $days_until_birthday
 * @property Collection<int, Contact> $contacts
 * @property LoyaltyCard|null $loyaltyCard
 */
#[ObservedBy([CustomerObserver::class])]
class Customer extends Model {
    /** @use HasFactory<CustomerFactory> */
    use Pageable, Authorable, HasFactory;

    protected $with = ['contacts', 'loyaltyCard'];

    protected $fillable = [
        'birthday', 'first_name', 'last_name', 'gender', 'note'
    ];

    /** @return HasMany<Contact, $this> */
    public function contacts(): HasMany {
        return $this->hasMany(Contact::class, 'customer_id', 'id');
    }

    /** @return HasOne<LoyaltyCard, $this> */
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
