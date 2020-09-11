<?php

namespace App\Http\Controllers;

//Contain the requets class
use Illuminate\Http\Request;
//With Hash we encrypt the dalicate data
use Illuminate\Support\Facades\Hash;
//this let us use the schema class to create the table
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
//with user we made a instace of the table 
use App\User;


class UserController extends Controller
{
    /**
     * Create all tables used in list application ("users","lists","tasks") in database 
     * 
     * @return void
     */

    public function createAllTables()
    {
        //define and create the schema of the table users 
        Schema::create('users', function (Blueprint $table) {
            $table->increments("user_id");
            $table->string("username",15)->unique();
            $table->string("email",40)->unique();
            $table->string("password",100);
        });
        
        //define and create the schema of the table lists 
        Schema::create('lists', function (Blueprint $table) {
            $table->increments("list_id")->unique();
            $table->string("list_name",45);
            $table->integer("user_id")->unsigned();
            $table->foreign("user_id")->references('user_id')->on('users');
            $table->string("status",11);
            $table->string("created",16);
            $table->string("finished",16);
        });

        //define and create the schema of the table tasks 
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments("task_id")->unique();
            $table->string("task",255);
            $table->integer("list_id")->unsigned();
            $table->foreign("list_id")->references('list_id')->on('lists');
            $table->string("status",11);
            $table->string("created",16);
            $table->string("finished",16);
        });
    }

    /**
     * Store a new user in database
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return void 
     */

    public function insertNewUser(Request $request)
    {
        //we create a new instance of the table 
        $newuser=new User;
        //we save values from request into the properties of user instance
        $newuser->username=$request->username;
        $newuser->email=$request->email;
        //encrypt the password with laravel function hash()
        $newuser->password=Hash::make($request->password);
        //save new user in database
        $newuser->save();  
    }

    /**
     * Handle the register for a new user in data base, make sure that user is not exist in database and the table users has been created before
     * 
     * @param \Illuminate\Http\Request $request
     * @return string $response 
     */

    public function registerUserData(Request $request)
    {
        $response="";
        //check if the table exist
        if(Schema::hasTable('users')){
            //make query in database searching by username and email
            $existUsername=User::where("username",$request->username)->first();
            $existEmail=User::where("email",$request->email)->first();
            //we check if the username or email exist 
            if(!$existUsername && !$existEmail){
                $response="We insert the new user";
                UserController::insertNewUser($request);
            }else{
                //we don't insert the user and return a answer to change the input message
                $response=($existUsername && $existEmail) ? "username and email exist": ($existUsername ? "username exist" : "email exist");
            }
        }else{
            //we create a table and insert the new user
            UserController::createAllTables(); 
            UserController::insertNewUser($request);
            $response="The table was created, and insert the new user.";
        }
        return $response;
    }

    /**
     * Encrypt the user data, by porpuse is not comented
     * 
     * @param string $dataUser
     * @return array
     */
    
    public function charEncrypt($dataUser){
        $userArray=[];
        $randomArray=[];
        foreach(str_split($dataUser) as $index=>$char){
            $randomNumer=rand(0,9);
            $userArray[$index]=strval(ord($char)+$randomNumer);
            $randomArray[$index]=$randomNumer;
        };
        return [$userArray,implode("",$randomArray)];
    }

    /**
     * Handle the login of user searching the user in database and checking the password 
     *
     * @param Request $request
     * @return array $response - Contain the status of the request 
     */

    public function loginUser(Request $request){
        //check username and email in database
        $response=["The username or email is incorrect or is not registered","",""];
        try {
            //try to get user if table exist and if user is finded in the table "users"
            // query make by ORM [ SELECT * FROM users WHERE username=$username OR email=$userEmail ;]
            $user=User::where("username",$request->usernameEmail)
                           ->orWhere("email",$request->usernameEmail)
                           ->first();
            if($user){
            //we check the user password with laravel check() function is are equal return the status and the encrypted user data
            Hash::check($request->password, $user->password) ? ($response[0]="Correct password" AND $response[1]=UserController::charEncrypt($user->user_id."-".$user->username))
                                                             :  $response[0]="Incorrect password";
            }
        } catch (\Throwable $error) {
            $response[2]=$error;
        }
        return $response;
    }

    /**
     * decode a encrypted user information 
     *
     * @param string $userData
     * @param string $randomKey
     * @return array $userDecodeData
     */

    public function decode($userData,$randomKey){
        $chars=explode(".",$userData);
        $keys=str_split($randomKey);
        $correctChars=[];
        $userDecodeData=[null,null];
        if(count($chars)==count($keys)){
            foreach($chars as $index=>$char){
                $correctChars[$index]=chr($char-$keys[$index]);
            };
            $decodeString=explode("-",implode($correctChars));
            $userDecodeData=[$decodeString[0],$decodeString[1]];
        }
        return $userDecodeData;
    }

    /**
     * Made query to get all user tasks and arrange tasks in arrangements for each list
     * @param string $userId - Id of user
     * @return $allUserTasksListed -This are array with arrays which tasks from every list
     */

    public function getUserListOfTasks($userId){
        // QUERY APPLY BY ORM -> [SELECT tasks.* FROM (( users INNER JOIN lists ON users.user_id=lists.user_id) INNER JOIN tasks ON lists.list_id=tasks.list_id) WHERE users.user_id=$userID ORDER BY list_id;]
        // get all task from the user 
        $allUserTasks=DB::table('users')
                        ->join('lists', 'users.user_id', '=', 'lists.user_id')
                        ->join('tasks', 'lists.list_id', '=', 'tasks.list_id')
                        ->select('tasks.*')
                        ->where("users.user_id",$userId)
                        ->orderBy('list_id')
                        ->orderBy('task_id')
                        ->get();
        //handle teh cases when the number of task of user are 0 and 1
        $allUserTasksListed=sizeof($allUserTasks)==0 ? [] : [$allUserTasks];
        if (sizeof($allUserTasks)>1) {
                //Array with all task saved in arrays for every list
            $allUserTasksListed=[[]];
            //this benchmark index at start will be the first list_id from the first task, with this we can know when task is from a new list 
            $benchmarkIndex=$allUserTasks[0]->list_id;
            //sort all task in individual arrays for each list
            foreach ($allUserTasks as $task) {
                if ($benchmarkIndex==$task->list_id){
                    //we add the task when are from the same list
                    array_push($allUserTasksListed[sizeof($allUserTasksListed)-1],$task);
                }else{
                    //we add new array
                    array_push($allUserTasksListed,[]);
                    //we change the listID
                    $benchmarkIndex=$task->list_id;
                    //we add the task to new list 
                    array_push($allUserTasksListed[sizeof($allUserTasksListed)-1],$task);
                }
            }
        }
        return $allUserTasksListed;
    }

    /**
     * check in database the user data to verify if is correct or corrupted and get lists and task of user
     *
     * @param Request $request
     * @return array $response - Return [usersStatus,[lists of user],[tasks of user]]
     */

    public function checkUserData(Request $request){
        //decode the information
        $user=UserController::decode($request->ude,$request->rke);
        $response= ["Corrupted cookie!!!",[],[]];
        //make a query to verify if the user exist 
        $existUser=User::where("user_id",$user[0])->first();
        if($existUser){
            $response[0]= $user[1]==$existUser->username ? "Correct cookie" : "Corrupted cookie!!!";
            //if user id saved in cookies are valid we seach the lists and task os user
            if($response[0]='Correct cookie'){
                //check the lists of the user and convert the array objec  t into array key value
                //QUERY APPLY BY ORM -> [SELECT list_id,list_name,status,created,finished FROM lists WHERE user_id=$userID;]
                $allUserLists=DB::table('lists')
                                ->select('list_id','list_name','status','created','finished')
                                ->where("user_id",$user[0])
                                ->get();
                //we saved and return with the response
                $response[1]=$allUserLists;
                //finally return in response all user task 
                $response[2]=UserController::getUserListOfTasks($user[0]);
            }
        }
        return $response;
    }

    /**
     * Update user information 
     * @param \Illuminate\Http\Request $request
     * @return string return the status of action
     */
    //!FUNCTION FOR VERSION 1.1
    public function updateUserData(Request $request)
    {
        //decode data from user with decode function 
        //find user in data base 
        $user=User::find($request->id);
        //changes values
        $user->username=$request->username;
        $user->email=$request->email;
        $user->password=$request->password;
        $user->save();
        return "enter to update ";
    }

    /**
     * Delete current user and destroy all the data (lits and task)
     * @param \Illuminate\Http\Request $request
     * @return string return the status of action
     */
    //!FUNCTION FOR VERSION 1.1
    public function deleteUser(Request $request)
    {
        $user=User::find($request->id);
        $user->delete();
        return "you delete the user";
    }
}
