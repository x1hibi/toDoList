<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ToDoList extends Model
{
    //especify the table name to avoid ToDoList as default
    protected $table = 'lists';
    //especify the primary key to avoid id as default
    protected $primaryKey = 'list_id';
    protected $fillable=["list_name","user_id","status","created","finished"];
    public $timestamps = false;
}
