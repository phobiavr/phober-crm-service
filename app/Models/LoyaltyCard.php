<?php

namespace App\Models;

use Database\Factories\LoyaltyCardFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string|null $code
 * @property string|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class LoyaltyCard extends Model {
    /** @use HasFactory<LoyaltyCardFactory> */
    use HasFactory;

    protected $table = 'loyalty_cards';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = ['id', 'code', 'status'];

    /** @return BelongsTo<Customer, $this> */
    public function customer(): BelongsTo {
        return $this->belongsTo(Customer::class, 'id', 'id');
    }
}
