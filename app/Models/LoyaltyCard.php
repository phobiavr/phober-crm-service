<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyCard extends Model {
    use HasFactory;

    protected $table = 'loyalty_cards';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $fillable = ['id', 'code', 'status'];

    public function customer(): BelongsTo {
        return $this->belongsTo(Customer::class, 'id', 'id');
    }
}
