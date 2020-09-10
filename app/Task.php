<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //especify the primary key to avoid id as default
    protected $primaryKey ='task_id';
    protected $fillable=["task","list_id","status","created","finished"];
    public $timestamps = false;
}
