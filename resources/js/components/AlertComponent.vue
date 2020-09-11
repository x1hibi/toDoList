<template>
    <div class="alertContainer">
        <div class="modal">
            <div class="headerList headerAlert" :class="title" style="padding:20px 0">
                <p class="title"></p>
            </div>
            <div class="alertBody">
                {{messageFirst}} <br> {{messageSecond}}
            </div>
            <div class="buttonContainer">
                <button type="button" class="buttonBlue fas fa-check" @click="deletionType(currentItem,id)"></button>
                <button type="button" class="buttonBlue fas fa-times" @click="animationCloseAlert()"></button>
            </div>
        </div>
    </div>
</template>

<script>
import item from './ItemComponent'
    export default {
        props:{
            currentItem:Number,
            id:Number,
            messageFirst:String,
            messageSecond:String,
            title:String,
        },
        data(){
            return {
                listOfLists:this.$parent.listOfLists,
            }
        },
        methods:{

            /**
             * Apply the close animation and remove alert
             */

            animationCloseAlert(){
                //start animation when opacity change
                document.getElementsByClassName("alertContainer")[0].style.opacity="0"
                // remove the modal after finish animation time
                setTimeout(() => {this.$parent.showAlert=false}, 300);
            },

            /**
             * Check the list where ask for deletion, and decide how delete
             * @param {(Number|String)} index - Index of the item to delete
             * @param {(Number|String)} id - Id of the item to delete
             */

            deletionType(index,id){
                //if you are delete a example list you only need to remove from the current list, otherwise you shoud save changes
                this.$parent.displaySection=='example' ? (this.$parent.exampleData.splice(index,1),this.animationCloseAlert()) 
                                                       : this.confirmDeletion(index,id)
            },
            
            /**
             * Confirm action to delete the current item and save changes correcly in local variables and local storage
             * @param {(Number|String)} index - Index of the item to delete
             * @param {(Number|String)} id - Id of the item to delete
             */
            
            confirmDeletion(index,id){
                //we block the check button to avoid error when save changes
                document.getElementsByClassName("fa-check")[0].disabled=true
                const isListOfList=document.getElementsByClassName("modalContainer")[0]!=undefined
                //remove from the displayed list
                let selectedList= isListOfList ? 'listOfLists' : 'listOfTasks'
                this.$parent[selectedList].splice(index,1)
                this.animationCloseAlert()
                // get correct id
                let idOfList= isListOfList ? id : this.$parent.currentListId
                //get index of the current array with local backup of items
                let backupIndex=this.$parent.listOfTasksLocalBackup.findIndex(list=>list[0].list_id==idOfList)

                if(isListOfList){
                    //get all modifications of all task without modidications of task from the deleted list and saved
                    let newModifiedTasks=this.$parent.modifiedTasks.filter(item => item.list_id != id)
                    this.$parent.modifiedTasks=newModifiedTasks
                    // get all list withput the deleted list and saved
                    let newDeletedTasks=this.$parent.deletedTasks.filter(item => item.listId != id)
                    this.$parent.deletedTasks=newDeletedTasks

                    //we check if the list exist in variable in local backup
                    if(backupIndex>=0){
                        //delete from local backup 
                        this.$parent.listOfTasksLocalBackup.splice(backupIndex,1)
                    }
                    //if there is a task and the number of task is one or zero
                }else if(!isListOfList && this.$parent.listOfTasksLocalBackup[backupIndex].length<=1 ){
                    //delete the last task and the array
                    this.$parent.listOfTasksLocalBackup.splice(backupIndex,1)
                }   
            
                //check the size of the current and change the status in list 
                !isListOfList ? this.$parent.checkAndModifyStatusList() : ''
                //check if exits modification of the current item 
                let existModification=this.$parent.hasBeenModifiedThisItem(id,isListOfList ? true : false)
                if(existModification!==false){
                    //if exist a task or list modified we remove it
                    this.$parent[isListOfList ? 'modifiedLists' : 'modifiedTasks'].splice(existModification,1)
                }
                
                //add the list/task to the deleted array
                if(id>=0){
                    isListOfList ? this.$parent.deletedLists.push(id) : this.$parent.deletedTasks.push({taskId:id,listId:this.$parent.currentListId})
                }
                //refresh current list to refresh DOM and see the correct order in the HTML 
                this.$parent.refreshList(selectedList,this.$parent[selectedList].slice(0))
            }
        },
        components:{
            'item-component':item,
        }
    }

</script>

<style scoped>

.alertContainer{
    align-items: center;
    background-color: rgba(0, 0, 0, .75);
    display: flex;
    height: 100% ;
    justify-content: center;
    left: 0;
    opacity: 0;
    position: fixed;
    right: 0;
    top: 0;
    transition: all 0.5s ease;
    width: 100%;
    z-index: 3;
}

.modal {
    -webkit-border-radius: 18px;
    background-size: cover;
    background: linear-gradient(cyan,rgb(238, 227, 227),rgb(165, 206, 224));
    border-radius: 18px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 85);
    box-sizing: content-box;
    font-family: 'Open Sans', sans-serif;
    height: fit-content;
    height: fit-content;
    margin: 0;
    overflow-x: hidden;
    overflow-y: auto;
    scrollbar-width: thin;
    transition: all .3s ease;
    width: 62%;
}

.headerAlert{
    color: whitesmoke;
    font-size: 60px;
    text-align: center;
    text-shadow: 0 0 10px darkblue;
}

.alertBody{
    color:white;
    font-family: 'Ubuntu', sans-serif;
    font-size: 20px;
    font-weight: 700 !important;
    height: fit-content;
    padding: 10px 10px 0 10px;
    text-align: center;
    text-shadow: 0 0 3px black;
    word-break: break-all;
}

.buttonContainer{
    display: grid;
    grid-template-columns: 1fr 1fr ;
    height: 10%;
    margin: 10px 0;
}

.buttonContainer button{
    align-self: center;
    box-shadow: 0 0 5px 0px gray;
    height:50px;
    justify-self: center;
    padding:0;
    width:50px;
}

@media (min-width: 769px){
    .modal{
        width: 45%;
    }
}

</style>