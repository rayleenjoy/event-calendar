<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    protected $table = 'event';
    protected $primaryKey = 'id';
    protected $fillable = ['*'];

}
