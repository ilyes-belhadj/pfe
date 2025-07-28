<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    public $incrementing = false; // Because we use uuid
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'title',
        'client',
        'email',
        'phone',
        'description',
        'address',
        'budget',
        'status',
        'progress',
        'redirect_to_plan_request',
        'agency',
        'commercial_name',
        'facade_color',
        'garage_type',
        'garage_dimensions',
        'heating_type',
        'house_dimensions',
        'house_type',
        'kitchen_type',
        'livable_area',
        'living_room_size',
        'bedrooms_count',
        'bedrooms_size',
        'bathrooms_count',
        'basement_type',
        'roof_angle',
        'tile_color',
        'window_type',
        'additional_options',
        'start_date',
        'deadline',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'progress' => 'integer',
        'redirect_to_plan_request' => 'boolean',
        'livable_area' => 'decimal:2',
        'living_room_size' => 'decimal:2',
        'roof_angle' => 'decimal:2',
        'additional_options' => 'array',
        'start_date' => 'datetime',
        'deadline' => 'datetime',
    ];
}
