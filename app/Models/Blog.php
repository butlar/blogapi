<?php

namespace App\Models;

use User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Blog extends Model
{
    use HasFactory, Notifiable;



    protected $fillable = [
        'user_id',
        'title',
        'description',
        'name',
        'read_time',
        'content',
        'image',
        'tags',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusAttribute($value)
    {
        return $value == '1' ? true : false;
    }
    public function getImageAttribute($value)
    {
        return url('/') . $value;
    }

     public function getCreatedAtAttribute($date)
    {
        return  date('M-d', strtotime($date)) ;
    }

    public function getUpdatedAtAttribute($date)
    {
        return  date('M-d', strtotime($date)) ;
    }
}
