<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Shared\Pageable\Pageable;

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
}
