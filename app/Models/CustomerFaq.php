<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerFaq extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'answer1',
        'answer2',
        'answer3',
        'answer4',
        'answer5',
        'answer6',
        'answer7',
        'answer8',
        'answer9',
        'answer10',
        'answer11',
        'answer12',
        'answer13',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
