<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Slider extends Model
{
    use HasFactory;

    protected $table = 'sliders';
    protected $fillable = ['image', 'link'];
    protected $appends = ['created_at_formatted'];

    public function getCreatedAtFormattedAttribute()
    {
        return Carbon::parse($this->created_at)->format('H:i:s d-m-Y');
    }
    
}
