<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    
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
     * add all new task created by user in table "lists" 
     *
     * @param array $dataOfTasks - Array with all task to create
     * @return string $status
     */

    public function addTask($dataOfTasks){
        $status='';
        try {
            //iterate over array $dataOfLists to add every new list 
            foreach ($dataOfTasks as $task) {
                //create a new instance of ToDoList table
                $newTask=new Task;
                //Assign the properties of instace of object ToDoList 
                $newTask->task=$task["task"];
                $newTask->list_id=$task["list_id"];
                $newTask->status=$task["status"];
                $newTask->created=$task["created"];
                $newTask->finished=$task["finished"]==null ? '' : $task["finished"];
                //save new changes 
                $newTask->save();
            }
            $status="ok";
        } catch (\Throwable $th) {
            $status=["error to add task!!!",$th];
        }
        return $status;
    }

    /**
     * Made query to get all user tasks and arrange tasks in arrangements for each task
     * @param string $userId - Id of user
     * @return $allUserTasksListed -This are array with arrays which tasks from every task
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
                        ->get();

        //handle teh cases when the number of task of user are 0 and 1
        $allUserTasksListed=sizeof($allUserTasks)==0 ? [] : [$allUserTasks];
        //Array with all task saved in arrays for every list
        if (sizeof($allUserTasks)>1) {
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
     * apply all modification in task of the user
     *
     * @param array $dataOfTasks
     * @return void
     */
    public function modifyTask($dataOfTasks){
        $status='';
        try{
            foreach ($dataOfTasks as $index => $task) {
                //we get the current list to modified
                $modifiedList=Task::find($task["task_id"]);
                $modifiedList->task=$task["task"];
                $modifiedList->status=$task["status"];
                $modifiedList->created=$task["created"];
                $modifiedList->finished=$task["finished"]==null ? '' : $task["finished"];
                $modifiedList->save();
            }
            $status="ok";
        } catch (\Throwable $th) {
            $status=["error to modify task",$th];
        }
        return $status;
    }

    /**
     * delete all the task deleted by user 
     *
     * @param array $dataOfTasks
     * @return string $status
     */

    public function deleteTask($dataTasks){
        $status='';
        try{
            //after delete the list/list
            Task::destroy($dataTasks);
            $status="ok";
        } catch (\Throwable $th) {
            $status=["error to delete task",$th];
        }
        return $status;
    }

    
    /**
     * handle all modifications in "Tasks" table add, modify and deleted changes in user tasks
     *
     * @param Request $request
     * @return string $status
     */
    public function saveChanges(Request $request)
    {
        //we separate the modifications if exits
        $toDelete=$request->deletedItems;
        $toModify=[];
        $toCreate=[];
        $idOfCreatedList=[];
        $status=['empty','empty','empty',"backup","allOk"];
        //decode encrypted userdata
        $userData=TaskController::decode($request->ude,$request->rke);
        if(sizeof($request->modifiedItems)>0){
            //check id and separate the items by type in two arrays, if id is less than cero means that the item is new
            foreach ($request->modifiedItems as $index => $item) {
                $item["task_id"]<0 ? $toCreate[$index]=$item : $toModify[$index]=$item;
            }
        }

        if(sizeof($toCreate)>0){
            //we add all the new tasks in request
            $addStatus=TaskController::addTask($toCreate);
            //return the status and error info in caso to get a error from database
            if ($addStatus=='ok') {
                $status[0]=$addStatus;
            }else{
                $status[4]=$addStatus[1];
                $status[0]=$addStatus[0];
            }
        }
        
        if(sizeof($toModify)>0){
            //aply modifications to tasks in request 
            $modifyStatus=TaskController::modifyTask($toModify);
            if ($modifyStatus=='ok') {
                $status[1]=$modifyStatus;
            }else{
                $status[4]=$modifyStatus[1];
                $status[1]=$modifyStatus[0];
            }
        }
        
        if(sizeof($toDelete)>0){
            //delete tasks in request 
            $deleteStatus=TaskController::deleteTask($toDelete);
            if ($deleteStatus=='ok') {
                $status[2]=$deleteStatus;
            }else{
                $status[4]=$deleteStatus[1];
                $status[2]=$deleteStatus[0];
            }
        }

        //make query and get all list from user and return into the view
        $status[3]=TaskController::getUserListOfTasks($userData[0]);

        return $status;
    }



}
