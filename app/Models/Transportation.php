<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transportation extends Model
{
    protected $table = 'transportations';

    protected $fillable = [
        'destination_id',
        'company_name',
        'company_document',
        'email',
        'contact_no',
        'address',
        'mode_of_transportation',
        'vehicle_type',
        'options',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_id');
    }
}
