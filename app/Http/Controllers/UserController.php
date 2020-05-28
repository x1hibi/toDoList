<?php

namespace App\Http\Controllers;

//Contain the requets class
use Illuminate\Http\Request;
//With Hash we encrypt the dalicate data
use Illuminate\Support\Facades\Hash;
//this let us use the schema class to create the table
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
//with user we made a instace of the table 
use App\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data=User::all();
        return [$data[0]->id,$data[0]->username];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments("id");
            $table->string("username",25)->unique();
            $table->string("email",50)->unique();
            $table->string("password");
        });
        echo "entre!";
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Schema::hasTable('users')){
            //we create a new instance of the table and add the make a insert 
            $newuser=new User;
            $newuser->username=$request->username;
            $newuser->email=$request->email;
            $newuser->password=Hash::make($request->password);
            $newuser->save();
            return "we insert a new user!";
        }else{
            //if the table don't exist we created and insert the values in the new table
            UserController::create();
            UserController::store($request);
        }
           
        //we create a instace of the model user and used eloquent to make a insert into the table users
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $query=User::where("password","patitas")->get();
        return $query;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function update(Request $request)
    {
        $user=User::find($request->id);
        $user->username=$request->username;
        $user->email=$request->email;
        $user->password=$request->password;
        $user->save();
        return "enter to update ";
    }

    public function pass(Request $request)
    {
        $userPass=User::find(7)->password;
        $currentPass=$request->password;
        if(Hash::check($currentPass,$userPass)){
            echo "valida";
        }else{
            echo "no valida";
        }
        
        return $currentPass;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $user=User::find($request->id);
        $user->delete();
        return "you delete the user";
    }
}
