<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class CustomerProfile extends Model
{
    use HasFactory;

    // EITHER open everything:
    // protected $guarded = [];  // <-- simplest during development

    // OR explicitly whitelist:
    protected $fillable = [
        'user_id',
        'first_name', 'last_name', 'phone',

        'ship_full_name', 'ship_line1', 'ship_line2',
        'ship_city', 'ship_state', 'ship_postal_code',
        'ship_country', 'ship_landmark', 'ship_is_default',

        'has_separate_billing',
        'bill_full_name', 'bill_line1', 'bill_line2',
        'bill_city', 'bill_state', 'bill_postal_code', 'bill_country',
    ];

    protected $casts = [
        'ship_is_default'       => 'boolean',
        'has_separate_billing'  => 'boolean',
    ];

    // Normalize 2-letter state codes
    public function setShipStateAttribute($value)
    {
        $this->attributes['ship_state'] = $value ? Str::upper(trim($value)) : null;
    }
    public function setBillStateAttribute($value)
    {
        $this->attributes['bill_state'] = $value ? Str::upper(trim($value)) : null;
    }
}
