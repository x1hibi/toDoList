<template>
    <div id="list" class="list">
        <div id="headerList" class="headerList"><h1 class="title">{{headerList}}</h1    ></div>
        <div id="bodyList" class="bodyList" >
            <section id="example" v-if="displaySection=='example'" class="fade">
                <item-component v-for="(item,index) in exampleData" :key="index" :index="index" :id="item.task_id" :value="item.task" :status="item.status" :created="item.created" :finished="item.finished"></item-component>
            </section >
            <section v-if="displaySection=='list'" class="fade">
                <item-component v-for="(item,index) in listOfTasks" :key="index" :index="index" :id="item.task_id" :value="item.task" :status="item.status" :created="item.created" :finished="item.finished"></item-component>
            </section>
            <login-component id="login" v-if="displaySection=='login'" type='login' class="fade"></login-component>
            <login-component id="register" v-if="displaySection=='register'" type='register' class="fade"></login-component>
            <div class="buttonsContainer" v-if="(displaySection=='example' || displaySection=='list') && notTyped">
                <button type="button" class="buttonBlue fas fa-plus" @click="addItemToList(true)" style="width:50px;height:50px;font-size:25px;padding:13px;box-shadow: 0 0 5px 0px gray;"></button>
                <!-- <button type="button" class="buttonBlue far fa-save" @click="saveChangesInDB('/api/saveModificationsToUserLists','modifiedLists','deletedLists','listOfLists')" style="width:50px;height:50px;font-size:25px;padding:0;box-shadow: 0 0 5px 0px gray;"><i class="fas fa-spin" style="display:none;" :disabled="displaySection=='example' ? true : false">&#xf1ce;</i></button> -->
            </div>
        </div>
        <div v-if="loggedIn && notTyped" >
            <menu-component class="buttonFixed" @click="showList()" option="list" :positionY="loginPosition" toltip="Display your lists" disabled="true"></menu-component>
            <menu-component class="buttonFixed" @click="setAndDeleteEncryptCookie('logout')" option="logout" :positionY="registerPosition" toltip="Log Out"></menu-component>
            <!-- <menu-component class="buttonFixed" @click="fullscreen()" :option="fullscreenIcon" :positionY="fullscreenPosition" :toltip="fullscreenToltip"></menu-component> -->
            <menu-component class="buttonFixed" @click="showMenu()" :option="menuIcon" positionY="0" :toltip="menuToltip"></menu-component>
        </div>
        <div v-else-if="!loggedIn && notTyped">
            <menu-component class="buttonFixed" @click="changeBodyAndTitleOfSection('login','Login','Sign in')" :option="loginIcon" :positionY="loginPosition" :toltip="loginToltip" disabled="true"></menu-component>
            <menu-component class="buttonFixed" @click="changeBodyAndTitleOfSection('register','Register','Sign Up')" :option="registerIcon" :positionY="registerPosition" :toltip="registerToltip"></menu-component>
            <menu-component class="buttonFixed" @click="showMenu()" :option="menuIcon" positionY="0" :toltip="menuToltip"></menu-component>
        </div>
        <modal-component v-if="showModal" title="My lists 📑" :notTyped="notTyped"></modal-component>
        <alert-component v-if="showAlert" :title="titleAlert" :messageFirst="messageAlert" :messageSecond="messageAlert2" :currentItem="currentItem" :id="currentId"></alert-component>
    </div>
</template>

<script>
    import alert from './AlertComponent'
    import item from "./ItemComponent"
    import menu from "./MenuComponent"
    import login from "./LoginComponent"
    import modal from "./ModalComponent"
    export default {
        created(){
            this.getCookieAndSetBackup()
        },
        mounted() {
            //check the height and adjust
            this.deviceHeight=screen.availHeight
            this.adjustHeight()
            window.addEventListener("resize",()=>{
                this.adjustHeight()    
            })
        },
        data(){
            return {
                addedList:-1,
                addedTask:-1.1,
                currentFocus:document.getElementsByTagName("body")[0],
                currentId:0,
                currentItem:0,
                currentListId:'',
                currentListIndex:'',
                currentSection:'example',
                deletedLists:[],
                deletedTasks:[],
                deviceHeight:'',
                displaySection:'example',
                exampleData:[{id:1,task:'Finish homerwork 💻',list_id:0,status:'finished',created:"2020-09-08|10:00",finished:'2020-09-08|12:00'},
                             {id:2,task:'Clean dishes 😫',list_id:0,status:'finished',created:"2020-09-08|11:10",finished:'2020-09-08|11:50'},   
                             {id:3,task:'Go to Town Center 🏃',list_id:0,status:'finished',created:"2020-09-08|12:40",finished:'2020-09-08|13:00'},   
                             {id:4,task:'Eat a ice cream with my girlfriend 🤤🍦',list_id:0,status:'finished',created:"2020-09-08|14:00",finished:'2020-09-08|15:00'},   
                             {id:5,task:'Watch a movie 😂🤣',list_id:0,status:'finished',created:"2020-09-08|17:30",finished:'2020-09-08|19:30'},
                             {id:6,task:'Organize a new day... 😎',list_id:0,status:'unfinished',created:"2020-09-08|21:00",finished:''} 
                            ],
                fullscreenActive:false,
                fullscreenIcon:"fullscreen",
                fullscreenPosition:"0",
                fullscreenToltip:"Activate FullScreen",
                headerList:"Example list 🗒️",
                idsOfSavedListInDB:[],
                itemInEdition:'',
                itemTextEdit:'',
                listOfLists:[],
                listOfListsBackup:[],
                listOfTasks:[],
                listOfTasksBackup:[],
                listOfTasksLocalBackup:[],
                localBackupTaskIndex:false,
                loggedIn:false,
                loginIcon:'login',
                loginPosition:"0",
                loginToltip:"Sign in",
                menuDisplayed:false,
                menuIcon:'menu',
                menuToltip:'Display menu',
                messageAlert:'Are sure to delete',
                messageAlert2:"the list?",
                modifiedLists:[],
                modifiedTasks:[],
                notSavingChanges:true,
                notTyped:true,
                numberChangesNotSavedInDB:0,
                registerIcon:'register',
                registerPosition:"0",
                registerToltip:"Sing Up",
                rke:null,
                showAlert:false,
                showModal:false,
                titleAlert:"fas fa-bell",
                ude:null,
                }
            },

        methods:{

            /**
             * Display a customize console log with sintax Lable : message / variable
             * @param {String} label - Label this will be print in yellow color to identify more quickly and add automatically ":" symbol
             * @param {(*)} message - Mensaje / variable printed is displayed in color white
             * @returns {String} Console log customized 
             */

            debug(label,message){
            let Label=label[0].toUpperCase() + label.slice(1)
            return typeof(message)=="string" ? console.log(`%c ${Label}: %c${message}`,"color:yellow","color:cyan") 
                                             : console.log(`%c ${Label}:`,"color:white",message)
            },

            /**Handle the fullscreen button  */

            fullscreen(){
                this.showMenu()
                if(!this.fullscreenActive){
                    this.fullscreenToltip='Desactivate Fullscreen'
                    this.fullscreenIcon='standarScreen'
                    document.body.requestFullscreen()
                }else{
                    this.fullscreenToltip='Activate FullScreen'
                    this.fullscreenIcon='fullscreen'
                    document.exitFullscreen()
                }
                this.fullscreenActive=!this.fullscreenActive
                
            },
            /**
             * Handle the status of the current list when user delete / modifify a task
            */

            checkAndModifyStatusList(){
                //STEP 1 Check if teh list has more than one task 
                let sizeOfList=this.listOfTasks.length
                let currentStatus=this.listOfLists[this.currentListIndex].status 
                let newStatus= sizeOfList>0 && this.listOfTasks.find(task=>task.status=="unfinished")==undefined ? 'finished' : 'unfinished'
                if(currentStatus!=newStatus){
                    //Declare and get the current date and hour
                    let date=new Date()
                    let time=date.toLocaleTimeString().match(/\d+:\d{2}/g)[0]
                    let newDate= newStatus=='finished' ? date.toISOString().slice(0,10)+"|"+time : ''
                    //Change status of status and finished date
                    this.saveChanges(this.currentListIndex,this.currentListId,"listOfLists","status",newStatus)
                    this.saveChanges(this.currentListIndex,this.currentListId,"listOfLists","finished",newDate)
                }
            },

            /** 
            * Handles changes made to task / list and saves them to local variables and browser storage
            * @param {(Number|String)} index - TabIndex from the current element 
            * @param {(Number|String)} id - Id unique from each element
            * @param {String} listToModified - Name of array of list / task to modify
            * @param {String} propertyToModify - Property of the object task / list to change 
            * @param {String} newValue - Value that replaces the current value
            */ 

            async saveChanges(index,id,listToModified,propertyToModify,newValue){

                //STEP ONE Change the current value in displayed list
                this[listToModified][index][propertyToModify]=newValue

                //This line is to make see reactivity correctly
                if(propertyToModify=="list_name" || propertyToModify=="task" ){
                    document.getElementsByClassName("editableBorder")[0].textContent=newValue
                }

                //STEP TWO Check if has been modified the item and get the index of the modificaion list 
                let modificationIndex=this.hasBeenModifiedThisItem(id, listToModified=='listOfLists' ? true : false)
                
                //STEP THREE Get the current item and check if the current item is stored in database
                //declare the current object item, and the same we save as string and the varible for the item in database
                let currentItem=this[listToModified][index]   
                let itemDisplayed=JSON.stringify(currentItem)
                let itemInDB=''
                //search if the item to modified in array with the items is stored in DB and saved in itemInDB
                if(listToModified=='listOfLists'){
                    itemInDB=this.listOfListsBackup.find(item => item.list_id==id) != undefined ? this.listOfListsBackup.find(item => item.list_id==id) : ''
                }else{
                    let backupIndex=this.listOfTasksBackup.length>0 ? this.listOfTasksBackup.findIndex(item=>item[0].list_id==this.currentListId) : undefined
                    if(backupIndex>=0){
                        itemInDB=this.listOfTasksBackup[backupIndex].find(item => item.task_id==id) != undefined ? this.listOfTasksBackup[backupIndex].find(item => item.task_id==id) : ''
                    }
                }
                itemInDB=JSON.stringify(itemInDB)
                let itemId=listToModified=='listOfLists' ? 'list_id' : 'task_id'
                //STEP FOUR We compare both item objects in order to know if are equal or not, and if the id is less than 0 
                let modificationList=listToModified=='listOfLists' ? 'modifiedLists': 'modifiedTasks'
                if(modificationIndex===false){
                    //if index is less than one means that item is new and is added into the list or if the value is diferent from the original
                    currentItem[itemId]<0 || itemInDB!=itemDisplayed ? this[modificationList].push(currentItem) : ''
                }else{
                    // when the item has already modified we check remplace the information if are new new item and we deleted of the list if are al ready in the list and have the same value
                    currentItem[itemId]>=0 && itemInDB==itemDisplayed ? this[modificationList].splice(modificationIndex,1)
                                                                      : this[modificationList][modificationIndex]=currentItem
                }
                
                //STEP FIVE Save changes in local variables and the local storage from the navigator 
                await this.saveLocalTasks()
                await this.saveChangesInNavigator("save changes")
                return true 
            },

            /**
             * Saves tasks / list with all modifications to local storage that has not yet been saved by user
            */

            saveChangesInNavigator(label='default'){
                //save all changes in 
                let changesToSaveInLocalStorage={
                    addedList:this.addedList,
                    addedTask:this.addedTask,
                    modifiedLists:this.modifiedLists,
                    deletedLists:this.deletedLists,
                    listOfLists:this.listOfLists,
                    modifiedTasks:this.modifiedTasks,
                    deletedTasks:this.deletedTasks,
                    listOfTasksLocalBackup:this.listOfTasksLocalBackup,
                    }
                //save the object in the local storage of the current navigator
                localStorage.setItem('localChanges',JSON.stringify(changesToSaveInLocalStorage))
                return true
            },

            /**
             * get and apply localChanges saved in local storage in browser
             */

            applyChanges(){//we apply all the changes not saved in database
                //get the changes saved in local storage
                let localChanges=JSON.parse(localStorage.getItem('localChanges'))

                if(localChanges){
                    //we assign all the local change in the current variables
                    this.addedList=localChanges.addedList,
                    this.addedTask=localChanges.addedTask,
                    this.modifiedLists=localChanges.modifiedLists
                    this.modifiedTasks=localChanges.modifiedTasks,
                    this.deletedLists=localChanges.deletedLists
                    this.deletedTasks=localChanges.deletedTasks,
                    this.listOfTasksLocalBackup=localChanges.listOfTasksLocalBackup

                    //we refresh the current list of list, we delete all lists and after we push every list
                    this.refreshList('listOfLists',localChanges.listOfLists)
                }
            },

            /**
             * Save the array with the current list of tasks in local storage
             */

            saveLocalTasks(){
                let index = false
                let listToSave=this.listOfTasks.slice(0)

                // get the index of the current list if exits 
                this.listOfTasksLocalBackup.forEach((list,i)=>{
                    if(list.length>0 && this.currentListId==list[0].list_id){
                        index=i
                    }
                })
                
                // we can only modified when the list exist and push when lenght is more than one because in deleted function we remove list from local backup
                if(listToSave.length>0){
                    //we push or modified if list are save
                    index!==false ? this.listOfTasksLocalBackup[index]=listToSave : this.listOfTasksLocalBackup.push(listToSave)
                }

                return true
            },

            /**
             * Remove all item from the display list 
             * @param {Array<{listOrTaskProperties:(String|Number)}>} listToDelete - Array with the current List of tasks
             */

            deleteAll(listToDelete){
                 while(listToDelete.length!=0) {
                    listToDelete.pop()
                }
                return true
            },

            /**
             * Handle correctly the actions of pop and push to show the expected reactivity on teh current list 
             * @param {Array<{listOrTaskProperties:(String|Number)}>} listToRefresh - Array of task / list that will be deleted with pop
             * @param {Array<{listOrTaskProperties:(String|Number)}>} listToDisplay - Array of task / list that will be pushed and contain the changes apply 
            */

            async refreshList(listToRefresh,listToDisplay){
                //Delete all tasks / lists current list
                await this.deleteAll(this[listToRefresh])
                //Push every item in the new list this will be displayed  
                listToDisplay.forEach(item=>{
                    this[listToRefresh].push(item)
                })
                //Save changes in local storage
                await this.saveLocalTasks()
                this.saveChangesInNavigator("refresh") 
                return true
            },

            /**
             * Handle the aditon of list / task in the current list
             * @param {Boolean} isTask - Indicates that a task is added or not
             */

            addItemToList(isTask){
                let items=document.getElementsByClassName("editable")
                //Additon is allowed when list are empty or if the last task are not empty
                if(items.length==0 || items[items.length-1].textContent.length>0 ){
                    //Get the time of creation
                    let date=new Date()
                    let time=date.toLocaleTimeString().match(/\d+:\d{2}/g)[0]
                    let dateAndTime=date.toISOString().slice(0,10)+"|"+time
                    //Add the correspond item into the local variables
                    if(isTask){
                        //New task to create
                        let newTask={task_id:this.addedTask,task:'',list_id:this.currentListId,status:'unfinished',created:dateAndTime,finished:''}
                        //Add the new task in correspond varible for each list 
                        this.displaySection!='example' ? this.listOfTasks.push(newTask) : this.exampleData.push(newTask)
                        //Counter to the creation order
                        this.addedTask=this.addedTask-1
                    }else{
                        //Create new list, push in the variable 
                        let newList={list_id:this.addedList,list_name:'',status:'unfinished',created:dateAndTime,finished:''}
                        this.listOfLists.push(newList)
                        this.addedList=this.addedList-1
                    }
                    //changes apply in example list when user add
                    if(this.displaySection!='example'){
                        //Save changes in local storage
                        this.saveLocalTasks()                    
                        this.saveChangesInNavigator("add")
                        //update the status of the list if add a new task
                        isTask ? this.checkAndModifyStatusList() : ''
                    }

                    //we scroll down in the list
                    setTimeout(() => {
                        document.getElementsByClassName(isTask ? 'fade' : 'modalBody')[0].scrollTo({top:10000,left:0,behavior: 'smooth'})
                    }, 10);
                    //get all editor icons in html document
                    let editorIconElementsList=document.getElementsByClassName("fa-edit")
                    //wait 10 ms to make sure that new item is added in DOM to be able to select it
                    setTimeout(() => {
                        //We active editor of the new element created to be more fluid user exprience
                        editorIconElementsList[editorIconElementsList.length-1].click()
                    }, 10);
                }            
            },

            /**
             * Disable and active saveButton to avoid multiple request and set process animation, and also handle the visibility of the menu button 
             * @param {Boolean} disable - Status of save button to handle the style changes
             */

            disabledSaveButton(disable){
                //we block all functionalities from the list to avoid conflicts
                this.notSavingChanges=!disable
                //we get the save button element
                let buttonSave= disable ? document.getElementsByClassName("fa-save")[0] : document.getElementsByClassName("fa-spin")[0].parentElement;
                //we disable/active save button and set animation 
                buttonSave.disabled=disable
                buttonSave.classList=disable ? "buttonBlue far" : "buttonBlue far fa-save"
                buttonSave.children[0].style.display=disable ? "block" : "none"
            },

            /**
             * Remplace a list_id negative on modified task for the list id assign by the DB when the new list are saved and we set the new one to be able to save the new tasks in the correct list.
             * @param {Boolean} setNewListIdInTaskLocalBackup - Indicate if we remplace the id of task from the localstorage
             */
            setNewListIdOnModifiedTask(setNewListIdInTaskLocalBackup=false){
                //we remplace the list_id of all task created to be able to saved in data base
                if(setNewListIdInTaskLocalBackup){
                    this.listOfTasksLocalBackup.forEach(list=>{
                        list.forEach(task=>{
                            if(task.list_id[0]=="-"){
                                let indexWithNewId=task.list_id*-1-1
                                let newIdList=this.idsOfSavedListInDB[indexWithNewId]
                                task.list_id=newIdList
                            }
                        })
                    })
                }
                //we iterate over all modified task and change the negative list id from the new one
                this.modifiedTasks.forEach(task=>{
                    //check if list id is negative
                    if(task.list_id[0]=="-"){
                        //get the index in postive 
                        let indexWithNewId=task.list_id*-1-1
                        //search the new assigned list id 
                        let newIdList=this.idsOfSavedListInDB[indexWithNewId]
                        //change the current list id for the new one
                        task.list_id=newIdList
                    }
                })
                //we save changes on the local storage
                this.saveChangesInNavigator("set new list")
                return "ok"
            },

            /**
             * Handle to save modifications of all list in database, contained in arrays following[modifiedLists/modifiedTasks] and [deletedLists/deletedTasks]
             * @param {String} urlRequest - This url is to make a request to ListTableController / TaskTableController 
             * @param {String} listModifiedItems - Name of array with the modifications of lists / tasks
             * @param {String} listDeletedItems - Name of array with the of the lists / tasks deleted
             * @param {String} listOfElements - Name of array of List / tasks displayed in view 
             */

            async saveChangesInDB(urlRequest,listModifiedItems,listDeletedItems,listOfElements){
                this.disabledSaveButton(true)
                //We check if exits modifications on the item saved in database
                if(this[listModifiedItems].length || this[listDeletedItems].length){
                    //get only id of deleted tasks
                    let deletedItems=listOfElements=='listOfLists' ? this[listDeletedItems] : this[listDeletedItems].map(deletedTask=>deletedTask.taskId)
                    let changesToApplyDB= {modifiedItems:this[listModifiedItems],deletedItems:deletedItems,ude:this.ude,rke:this.rke} 
                    //start request with the select url to save modificaions in list table or user task table
                    await axios.post(urlRequest,changesToApplyDB).then(
                        async response=>{
                            let status=response.data
                            //check the status retuned in the response with regex
                            let regex=/^(empty|ok)$/g
                            //we check if all sql operations were executed succefully
                            if(status[0].match(regex) && status[1].match(regex) && status[2].match(regex) ){
                                //reset the values of list of modifications and deleted arrays
                                this[listModifiedItems]=[]
                                this[listDeletedItems]=[]
                                //we use JSON methods to convert the string into a correct object variable and make it immutable
                                this[listOfElements+"Backup"]=JSON.parse(JSON.stringify(status[3]))

                                //refresh DOM with the news elements id and the counter of new task / list
                                if(listOfElements=="listOfLists"){
                                    //Reset list counter 
                                    this.addedList=-1
                                    //Update DOM to see the id in data base in new elements
                                    this.refreshList(listOfElements,status[3].slice(0))
                                    //We changes the id of all task created before to save in DB
                                    //Save all id of the list saved in database
                                    this.idsOfSavedListInDB=response.data[4].reverse()
                                    //set new list id on new task
                                    await this.setNewListIdOnModifiedTask()
                                    //check for empty list of lists
                                    if(status[3].length===0){
                                        //set empty varibles when list are deleted 
                                        this.addedTask=-1.1 
                                        this.listOfTasksBackup=[]
                                        this.modifiedTasks=[]
                                        this.listOfTasksLocalBackup=[]
                                    }
                                    this.disabledSaveButton(false)
                                    this.saveChangesInDB('/api/saveModificationsToUserTasks','modifiedTasks','deletedTasks','listOfTasks')
                                }else if(listOfElements=="listOfTasks"){
                                    this.addedTask=-1.1
                                    this.disabledSaveButton(false)
                                    //Update the current list of task when a list of task is displyed s
                                    if(!this.showModal){
                                        this.refreshList(listOfElements,status[3][this.currentListIndex].slice(0))
                                    }
                                    //we save the new data from database in listOfTasksLocalBackup
                                    this.listOfTasksLocalBackup=status[3].slice(0)
                                    this.saveChangesInNavigator("save in db in list of task")
                                }                                
                            }else{
                                if(listOfElements=="listOfTasks"){
                                    //if task are not saved we change the id of all task created to be able to make sure that next time will be saved correctly 
                                    //code to change all id_list from the backup list and save changes
                                    //Display modal try later error in server for version 1.1!!!!
                                    await this.setNewListIdOnModifiedTask(true)
                                }
                                console.log("error in the server!")
                                this.disabledSaveButton(false)
                            }
                        }
                    ).catch(error=>{console.log(error)})
                }else{
                    //When you don't have list modifications you call again the function
                    this.disabledSaveButton(false)
                    listOfElements=="listOfLists" ? this.saveChangesInDB('/api/saveModificationsToUserTasks','modifiedTasks','deletedTasks','listOfTasks') : ''
                }
            },

            /**
             * Display in view all the task from the current list, and save information of the selected list in local variables
             * @param {(Number|String)} listId - Id of the selected list
             * @param {(Number|String)} listIndex - Index of the selected list
             * @param {(Number|String)} listName - Name of the selected list
             */

            setList(listId,listIndex,listName){
                //reset variables for editor
                this.itemTextEdit=''
                this.itemInEdition=''
                //save information of the selected list
                this.currentListId=listId
                this.headerList=listName
                let selectedIndex=false
                this.currentListIndex=listIndex
                //Check if local backup is empty and find which list user select
                this.listOfTasksLocalBackup.forEach((list,index)=>{
                    if(list[0] && list[0].list_id==listId){
                        selectedIndex=index
                        this.localBackupTaskIndex=index
                    }
                })
                //here we display the selected list if exist and have one or more tasks
                if(this.listOfTasksLocalBackup.length>0 && selectedIndex!==false){
                    //now we push every item from the selected list
                    this.listOfTasksLocalBackup[selectedIndex].forEach(task=>{
                        this.listOfTasks.push(task)
                    })
                }
                
                //we change styles to show animation for the modal 
                document.getElementsByClassName("modalContainer")[0].style.opacity=0
                setTimeout(() => {
                    this.showModal=false
                }, 500);
            },

            /**
             * Display modal with the list of user lists
             */

            showList(){
                //we reset the variable to avoid delete a list in the function saveLocalTasks()
                //reset variables for used for editor
                this.itemTextEdit=''
                this.itemInEdition=''
                this.localBackupTaskIndex=false
                //display the list of list and apply the animation
                this.showModal=true
                setTimeout(() => {document.getElementsByClassName("modalContainer")[0].style.opacity=1}, 10);
                this.showMenu()
                //we delete all the current name and task
                this.listOfTasks.splice(0,this.listOfTasks.length)
            },

            /**
             * Check the array of modifications of tasks / lists and look if the passed item is in the list 
             * @param {(String|Number)} idToCheck - Id used to compare all the modified elements 
             * @param {String} isListOfList - Name of the array with modification to search the passed id
             * @returns {(Number|Boolean)} Index of the item found, return false is not in the list 
             */

            hasBeenModifiedThisItem(idToCheck,isListOfList){
                //we declare array with modifications of tasks or lists
                let arrayWithModifications=this[isListOfList ? 'modifiedLists':'modifiedTasks']
                let id=isListOfList ? 'list_id' : 'task_id'
                let idInArray=false
                //we check every element inside of the modifed and check if the current element has been edited before and save the index
                for(let i=0;i<arrayWithModifications.length;i++){
                    if(arrayWithModifications[i][id]==idToCheck){
                        idInArray=i
                        break
                    }
                }
                //we return false if the element has not be modified before and a current index if has al ready modified
                return idInArray                          
            },

            /**
             * Check for the user information from cookies and auth user data is correct and made login and check if have lists saved in database and saved in local variables backup, after check in local storage and check if have changes and apply in current list
             */

            async getCookieAndSetBackup(){
                //STEP 1 get the cookie and save as variable in object
                this.ude=document.cookie.match(/(?<=ude=)[^\;]+/g) ? document.cookie.match(/(?<=ude=)[^\;]+/g)[0] : null
                this.rke=document.cookie.match(/(?<=rke=)[^\;]+/g) ? document.cookie.match(/(?<=rke=)[^\;]+/g)[0] : null
                //STEP 2 valid auth and return the list view and declare a correct login 
                if(this.ude && this.rke){
                    await axios.get('/api/authUserAndGetData',{params:{ude:this.ude,rke:this.rke}}).then(
                        response=>{
                            //Check the response status with a regex and save status
                            this.loggedIn=response.data[0].match(/Correct/g) ? (this.displaySection="list",true) : false
                            //save data list of list 
                            this.listOfLists=response.data[1]
                            //we save the list of lists database and make sure that array is inmutable
                            this.listOfListsBackup=JSON.parse(JSON.stringify(response.data[1]))
                            //save the lists of tasks 
                            this.listOfTasksLocalBackup=response.data[2]
                            //we save the list of task database and make sure that array is inmutable
                            this.listOfTasksBackup=JSON.parse(JSON.stringify(response.data[2]))
                            this.showModal=true
                            setTimeout(() => {document.getElementsByClassName("modalContainer")[0].style.opacity=1}, 10);
                        }
                    ).catch(error=>{console.log(error)})
                }
                //remove spinner when finish to charge all data     
                setTimeout(() => {
                    document.getElementsByClassName("spinner")[0].style.opacity=0
                    setTimeout(() => {this.$parent.spinner=false}, 500);
                }, 50);
                
                //STEP 4 check in local storage for changes not saved in list of user and apply if exists   
                this.applyChanges()
            },

           /**
            * Set and removes cookies with user data 
            * @param {String} action - Indicate if we want to set o remove cookie
            * @param {String} userData - User data encrypted
            * @param {String} randomKey - Key to decode user data    
            */

            setAndDeleteEncryptCookie(action,userData='',randomKey=''){
                let currentDate=new Date()
                let expiration=action=="set" ? 1 : -1
                currentDate.setMonth(currentDate.getMonth()+expiration)
                document.cookie=`ude=${userData};expires=${currentDate.toUTCString()};path='/';`
                document.cookie=`rke=${randomKey};expires=${currentDate.toUTCString()};path='/';`
                action=='logout' ? (location.reload(),localStorage.removeItem('localChanges')) : ''

            },

            /**
             * Dispay aplication options in floating buttons
             */

            showMenu(){
                //change positions of buttons to activate animation 
                this.menuDisplayed=!this.menuDisplayed
                this.menuIcon=this.menuDisplayed ? 'close' : 'menu'
                this.menuToltip=this.menuDisplayed ? 'Close menu' : 'Display menu'
                this.loginPosition= this.menuDisplayed ? "1" : "0"
                this.registerPosition=this.menuDisplayed ? "2" : "0"
                this.fullscreenPosition=this.menuDisplayed ? "3" : "0"
            },

            /**
             * Handle the changes of the title and body displayed in view and floating buttons, and makes sure of change styles correctly of them 
             * @param {String} selectedSection - Is the new section to display (login|register)
             * @param {String} titleSection - Header of list to display (Login|Register)
             * @param {String} toltipSection - Toltip to show in overmouse in floating buttons
             */

            changeBodyAndTitleOfSection(selectedSection,titleSection,toltipSection){
                //STEP 1 handle the style changes on the floating buttons
                let condition=this.currentSection!=selectedSection
                this[selectedSection+'Toltip']=condition ? 'List Editor' : toltipSection
                this[selectedSection+'Icon']=condition ? 'editor' : selectedSection
                selectedSection=='login' ? (this.registerIcon='register',this.registerToltip='Sing Up')
                                         : (this.loginIcon='login',this.loginToltip='Sing In')
                //Check and select the seccion Body and title to display
                let newBody=condition ? selectedSection : "example"
                let newTitle=condition ? titleSection  : "Example List"
                
                //STEP 2 Add the animation of the title setting the opacity in 0
                document.getElementsByClassName("title")[0].style.opacity=0
                document.getElementById(this.displaySection).style.opacity=0
                //Change current section and close menu buttons
                this.currentSection=newBody
                this.showMenu()
                //we wait to finish hide animation 
                setTimeout(() => {
                    //STEP 3 Change the current title and body of list 
                    this.headerList=newTitle
                    this.displaySection=newBody
                    //we wait to active the show animation 
                    setTimeout(() => {
                        //STEP 4 Active animation when change opacity and adjust heigth 
                        document.getElementsByClassName("title")[0].style.opacity=1
                        document.getElementById(newBody).style.opacity=1
                        this.adjustHeight()
                        //reset all inputs in value and style 
                        let currentInputs=[...document.getElementsByTagName("input")]
                        currentInputs.forEach(input=>{
                            input.onpaste=e=>e.preventDefault()
                            input.value=""
                            input.nextElementSibling.style.opacity=0
                            input.style.borderColor="rgb(155, 150, 240)"
                        })
                    }, 10);
                }, 250);

            },

            /**
             * Adjust the height of the view, and changes styles when view has some changes like keyboard, hide and show url and consider the current navigator 
             */

            adjustHeight(){
                //STEP 1 Get all varaibles used as conditions to evaluated the cases of event resize
                //Get orientation of device comparing the avail heigth and the avail width, we use this insted of innerHeight and client height because is value only changes when change the orientation of device
                let orientationOfDevice=window.screen.availHeight>window.screen.width ? 'portrait' : 'landscape'
                //Check if is a mobile device as Iphone or Android smartphone, we return boolean usig a regex function 
                let mobileDevice=/Android|iPhone/g.test(navigator.userAgent)
                //Calculate the diference in pixels between the size of the screen and the height of the windows
                //we calcualte diferent the current heightin standar scrren and fullscreen v1.2
                let currentHeight=document.fullscreen ? screen.height : window.innerHeight
                let currentHeightReference= document.fullscreen ? this.deviceHeight : window.screen.availHeight
                let diferenceOfPixelsInScreen= currentHeightReference-currentHeight
                //We use user agent and document.fullscrren to check the current browser and select a correct parameter parameter is status bar ~25px, urlbar ~50px, options browser bar 50px, android navigation bar 50 px, and extra 50 for precaution for non counted bars 50px 
                let browserParameter = /OPR/g.test(navigator.userAgent) ? 175+50 : /Firefox/g.test(navigator.userAgent) ? 125+50 : /Chrome/g.test(navigator.userAgent) ? 125+50 : 125+50
                //we we check the browser and the fullscreen to define the value
                //let statusBar= document.fullscreen && /Chrome/g.test(navigator.userAgent) ? 0 : document.fullscreen ? 0 : 25
                let statusBar=25
                //If the diference is bigger than 100 px means that keyboars is in the screen, we asume that size of status bar and url bar together add up are less than 100px in Chrome naviagtor every navigator has diferents values 
                let keyboardInScreen = diferenceOfPixelsInScreen > browserParameter
                //If the diference is more than 25px (size of status bar) but less than 100px means that url bar is in the navigator
                let urlBarInScreen = browserParameter > diferenceOfPixelsInScreen && diferenceOfPixelsInScreen > statusBar
                //STEP 2 Check if you are in mobile device and adjust heigth and chang styles
                if(mobileDevice){
                    //Remove bottons when keyboard is open
                    this.notTyped= keyboardInScreen ? false : true  
                    if(keyboardInScreen){
                        //we set the exact heigth in the element with id listApp
                        document.getElementById('listApp').style.height= `${window.innerHeight}px`
                        if(orientationOfDevice=='landscape'){
                            //we adjust some styles to be able to use correcty the aplication with less space
                            document.getElementById("headerList").style.display="none"
                            document.getElementById("bodyList").style.gridArea="1/1/3/2"
                            document.getElementById("bodyList").style.borderRadius="15px"
                            document.getElementById("bodyList").style.height=`${window.innerHeight}px`
                        }
                    }else if(urlBarInScreen){
                        //Styles to change when url bar is displayed in navigator 
                        document.getElementById('listApp').style.height= '100vh'
                        document.getElementById("headerList").style.display="grid"
                        document.getElementById("bodyList").style.gridArea="2/1/3/2"
                        document.getElementById("bodyList").style.borderRadius="0 0 15px 15px"
                        document.getElementById("bodyList").style.height='100%'
                        document.getElementById("list").style.height='100%' 
                    }

                //Adjust the size of the list when keyboard is open and close and also depending of the orientation 
                document.getElementById('list').style.height = (keyboardInScreen && orientationOfDevice=='landscape') ? `${window.innerHeight}px` 
                                                             : keyboardInScreen ? '' 
                                                             : (/(login|register)/g.test(this.displaySection) && orientationOfDevice=="portrait")? 'max-content' : ''
                    
                //Ajust the height of the modal when keyboard is open 
                if(document.getElementsByClassName('modalBody')[0]){
                    //get elements of modal to modify
                    let modalHeader=document.getElementsByClassName("headerList")[1]
                    let modalBody=document.getElementsByClassName("modalBody")[0]
                    //we adjust the height of the modal body to make scroll only in th body and fit perfetecly and adjust some style to better user exprience 
                    let modalBodyHeight=document.getElementsByClassName("modal")[0].clientHeight-modalHeader.clientHeight;
                    modalBody.style.height = (keyboardInScreen && orientationOfDevice=='landscape') ? (modalHeader.style.display='none',modalBody.style.paddingTop="5px",'100%') 
                                           : (keyboardInScreen && orientationOfDevice=='portrait')  ? '65%' : (modalHeader.style.display='grid',modalBody.style.paddingTop="",'calc(95% - 125px)')
                }
                                                                 
                //Handle the change of size of the list (grid rows procentage) when the navigator set or quit the url bar
                document.getElementById('listApp').style.gridTemplateRows = (/(example|list)/g.test(this.displaySection) & urlBarInScreen & orientationOfDevice=="portrait") ? '2.5% 86.5% 11%' 
                                                                          : (urlBarInScreen & orientationOfDevice=="landscape") ? '2.5% 78% 19.5%'
                                                                          : '2.5% 95% 2.5%'
                }else if(/(login|register)/g.test(this.displaySection)){
                    //Desktop adjust heigth 
                    document.getElementById("list").style.height='max-content'
                }else{
                    document.getElementById("list").style.height='100%'
                }
            }
        },
        components:{
            "item-component":item,
            "menu-component":menu,
            "login-component":login,
            "modal-component":modal,
            "alert-component":alert,
        }
    }
</script>

<style scoped>

.list{
    border-radius: 15px;
    border:1px solid lightblue;
    box-shadow: 5px 5px 10px 0 rgba(255, 182, 193, 0.5);
    box-sizing: border-box;
    display: grid;
    grid-area: 2/2/3/3;
    grid-template-rows: 110px 1fr;
    height: 100%;
    transition: all .3s ease;
}

.bodyList{
    background:linear-gradient(to top, rgb(235, 232, 232), whitesmoke,rgb(240, 232, 232));
    border-radius: 0 0 15px 15px;
    box-sizing: border-box;
    display: grid;
    grid-area: 2/1/3/2;
    grid-template-rows: 1fr auto;
    height: 100%;
    overflow-y: auto;
    padding:10px;
}

section{
    grid-area: 1/1/2/2;
    overflow-x: hidden;
}

.buttonsContainer{
    box-sizing: border-box;
    display: grid;
    grid-area: 2/1/3/2;
    grid-template-columns: 1fr;
    padding-top: 10px;
}

.buttonsContainer button{
    font-size: 1em;
}

.fade{
    animation-duration: 0.25s;
    animation-name: fadeIn;
    animation-timing-function:ease-in-out ;
    transition-duration: 0.5s;
}

@keyframes fadeIn{
    0%{opacity: 0;}
    100%{opacity: 1;}
}

</style>