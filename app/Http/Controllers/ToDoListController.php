<?php

namespace App\Http\Controllers;

use App\ToDoList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ToDoListController extends Controller
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
     * add all new list created by user in table "lists" 
     *
     * @param array $dataOfLists
     * @param int $userId
     * @return string $status
     */

    public function addList($dataOfLists,$userId){
        $status='';
        try {
            //iterate over array $dataOfLists to add every new list 
            foreach ($dataOfLists as $list) {
                //create a new instance of ToDoList table
                $newList=new ToDoList;
                //Assign the properties of instace of object ToDoList 
                $newList->list_name=$list["list_name"];
                $newList->user_id=$userId;
                $newList->status=$list["status"];
                $newList->created=$list["created"];
                $newList->finished=$list["finished"]==null ? '' : $list["finished"];
                //save new changes 
                $newList->save();
            }
            $status="ok";
        } catch (\Throwable $th) {
            $status=["error to add list!!!",$th];
        }
        return $status;
    }

    /**
     * delete all the list deleted by user 
     *
     * @param array $dataOfLists
     * @return string $status
     */

    public function deleteList($dataOfLists){
        $status='';
        try{
            //First delete all task from the list/list deleted to avoid errors in data base
            //delete action made by ORM [ DELETE FROM tasks WHERE list_id IN ($dataOfLists);]
            DB::table('tasks')
              ->whereIn('list_id',$dataOfLists)->delete();
            //after delete the list/list
            //delete action made by ORM [ DELETE FROM tasks WHERE list_id IN ($dataOfLists);]
            ToDoList::destroy($dataOfLists);
            $status="ok";
        } catch (\Throwable $th) {
            $status=["error to delete list",$th];
        }
        return $status;
    }

    /**
     * apply all modification in list of the user
     *
     * @param array $dataOfLists
     * @return void
     */
    public function modifyList($dataOfLists){
        $status='';
        try{
            foreach ($dataOfLists as $index => $list) {
                //we get the current list to modified
                $modifiedList=ToDoList::find($list["list_id"]);
                $modifiedList->list_name=$list["list_name"];
                $modifiedList->status=$list["status"];
                $modifiedList->created=$list["created"];
                $modifiedList->finished=$list["finished"]==null ? '' : $list["finished"];
                $modifiedList->save();
            }
            $status="ok";
        } catch (\Throwable $th) {
            $status=["error to modify list",$th];
        }
        return $status;
    }

    /**
     * handle all modifications in "lists" table add, modify and deleted changes in user lists
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
        // declare array which will contaian all response
        $status=['empty','empty','empty',"backup",[],"allOk"];
        //decode encrypted userdata
        $userData=ToDoListController::decode($request->ude,$request->rke);
        if(sizeof($request->modifiedItems)>0){
            //check id and separate the items by type in two arrays, if id is less than cero means that the item is new
            foreach ($request->modifiedItems as $index => $item) {
                $item["list_id"]<0 ? $toCreate[$index]=$item : $toModify[$index]=$item;
            }
        }

        if(sizeof($toCreate)>0){
            //we add all the new lists
            $addStatus=ToDoListController::addList($toCreate,$userData[0]);
            //return the status and error info in case to get a error from database
            if ($addStatus=='ok') {
                $status[0]=$addStatus;
            }else{
                $status[5]=$addStatus[1];
                $status[0]=$addStatus[0];
            }
            //we return the list with the id of the new lists
            $idOfCreatedList = ToDoList::orderBy('list_id', 'desc')->pluck('list_id')->take(sizeof($toCreate));
            $status[4]=$idOfCreatedList;
        }
        
        if(sizeof($toDelete)>0){
            //detele the list and task inside the list
            $deleteStatus=ToDoListController::deleteList($toDelete);
            if ($deleteStatus=='ok') {
                $status[2]=$deleteStatus;
            }else{
                $status[5]=$deleteStatus[1];
                $status[2]=$deleteStatus[0];
            }
        }

        if(sizeof($toModify)>0){
            //apply modification to the lists
            $modifyStatus=ToDoListController::modifyList($toModify);
            if ($modifyStatus=='ok') {
                $status[1]=$modifyStatus;
            }else{
                $status[5]=$modifyStatus[1];
                $status[1]=$modifyStatus[0];
            }
        
        }

        //make query and get all list from user and return into the view
        //query made by ORM [SELECT list_id,list_name,status,created,finished from lists WHERE user_id=$userID;]
        $status[3]=ToDoList::select('list_id','list_name','status','created','finished')->where("user_id",$userData[0])->get();

        return $status;
    }

}
