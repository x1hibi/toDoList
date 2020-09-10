<template>
    <div>
        <div class="itemContainer">
        <button-component type="editorOff" color="gray" @click="editItem($event,type)" ></button-component>
        <div :id="id" :tabindex="index" class="noOutliner" >
            <p class="itemText editable" contentEditable="false" :style="type ? 'font-weight:600' : 'font-weight:300'" @click="showAllInformationChecker($event,type,false)" @input="checkLenghtAndPaste($event,type)" title="Show All Information" data-placeholder="Add your task">{{value}}</p>
            <p class="itemText dateAndTime" v-if="show" @click="showAllInformationChecker($event,type,true)">{{'Created: '+created}} <br/> {{'Finished: '+finished}}</p>
        </div>
        <button-component type="delete" color="black"  @click="deleteItem($event,type)"></button-component>
        <button-component :type="newStatus" :color="starColor" :shadow="shadow" @click="ChangeStatus($event,type)"></button-component>
        </div>
        <hr/>
    </div>
</template>

<script>
import button from "./ButtonComponent"
    export default {
        props:{
            created:String,
            finished:String,
            id:Number,
            index:Number,
            status:String,
            type:String,
            value:String,
        },
        data(){
            return {
                animationInProcess:false,
                editor:'editorOff',
                newStatus:this.status,
                previousString:'',
                shadow:this.status=='finished' ? '0 0 1.5px rgb(30, 9, 255,0.5)' : '',
                show:false,
                starColor:this.status=='finished' ? 'yellow' : 'red',
            }
        },
        methods:{

            /**
             * Takes care of let open just one editor to avoid multiples editions 
             * @param {Object} currentP - Current element to edit 
             */

            onlyOneEditable(currentP){
                //get array of paragrahp elements with editor active and the icons of editor active 
                let pEditables=[...document.getElementsByClassName("editable")]
                let iconEdit=[...document.getElementsByClassName("fa-edit")]
                pEditables.forEach((editable,index)=>{
                    editable.scrollTo(0,0)
                    if(editable!=currentP){
                        //convert paragraph in a editable element and change styles and name class 
                        iconEdit[index].className="far fa-edit"
                        editable.contentEditable=false
                        editable.className= editable.className.match(/showItem/g) ? "itemText editable showItem" : "itemText editable"
                        editable.parentElement.className= editable.parentElement.className.match(/efectText/g) ? "noOutliner efectText" : "noOutliner"
                        editable.style.overflowY=''
                    }
                })
            },

            /**
             * Check every item in displayed list and set a red border if are empty 
             * @returns {Boolean} 
             */

            checkEmptyValues(){
                let items=[...document.getElementsByClassName("editable")]
                let emptyItems=0
                items.forEach(item=>{
                    //check every item to see if the value is empty ("") and set a red vorder 
                    if(item.textContent==""){
                        item.style.setProperty("border", "2px solid red", "important")
                        item.style.margin="0 2.5px"
                        emptyItems+=1
                    }
                })
                return emptyItems==0 ? true : false
            },

            /**
             * Get values from the modified item in list and send it as parameter in saveChanges() 
             * @param {String} type - indicate if component displayed (modalList indicate that is a list of list otherwise is list of task)
            */

            saveChangesList(type){
                //declare a pointer to the vue father instance in base of the type
                let father=type=="modalList" ? this.$parent.$parent : this.$parent;
                //check if editor is active the editor 
                if(document.getElementsByClassName("editableBorder")[0]!=undefined ){
                    let modifiedItem=document.getElementsByClassName("editableBorder")[0]
                    //let currentDisplayedArray=type=='modalList' ? 'listOfLists': 'listOfTasks'
                    let listToModified=type=='modalList' ? 'listOfLists': 'listOfTasks'
                    //let currentIndex=modifiedItem.parentElement.tabIndex
                    let elementIndex=modifiedItem.parentElement.tabIndex
                    let propertyToModify=type=='modalList' ? 'list_name': 'task'
                    //let backupList=type=='modalList' ? 'listOfListsBackup':
                    let modificationList=type=='modalList' ? 'modifiedLists': 'modifiedTasks'
                    //get the id of the currente element to modify
                    let elementId=modifiedItem.parentElement.id 
                    // save changes in local variables
                    father.saveChanges(elementIndex,elementId,listToModified,propertyToModify,modifiedItem.textContent)
                }
            },

            /**
             * Create a range and selection from the current editable item and focus the caret at the end
             * @param {Object} item - HTML element 
             */

            caretInLastCharacter(item){
                let range = document.createRange();
                let selection = window.getSelection();
                range.setStart(item.childNodes[0], item.textContent.length);
                range.collapse(true);
                selection.removeAllRanges();
                selection.addRange(range);
                item.focus();
            },
        
            /**
             * Activate editor in item and adjust styles and decide when save changes 
             * @param {Object} e - event from element 
             * @param {String} type - Indicate if is list of list or list of tasks
             */

            editItem(e,type){
                //get item from event
                let item=e.target.nextElementSibling.children[0]
                //save value of the item 
                this.previousString=item.textContent
                let father=type=="modalList" ? this.$parent.$parent : this.$parent;
                //check if the class of editor is active 
                if(document.getElementsByClassName("editableBorder")[0]!=undefined ){
                    //element can be empty so if is empty we return his last saved value
                    if(father.itemInEdition.textContent.match(/.*\S.*/g)===null){
                        //we reset the previus value in the list in order to avoid errors
                        father.itemInEdition.textContent=father.itemTextEdit
                    //save chenges in list of list if input is diferente from the last saved change and list of task, except for example list
                    }else if(father.itemTextEdit!=father.itemInEdition.textContent){
                        father.displaySection!='example' ? this.saveChangesList(type) : ''
                    }
                }
                //save last modification and the element
                father.itemTextEdit=item.textContent.slice(0)
                father.itemInEdition=item
                item.style.border=""
                //make sure that is only editor active
                this.onlyOneEditable(item)
                //we close more information 
                this.show=false
                //change styles
                item.className= item.className.match(/editableBorder/g) ? 'itemText editable' : 'itemText editable editableBorder'
                //item.classList.toggle("editableContainer")
                item.parentElement.className= item.parentElement.className.match(/editableContainer/g) ? 'noOutliner' : 'noOutliner editableContainer'
                e.target.className=e.target.className=="far fa-edit" ? "fas fa-edit popAnimation" : "far fa-edit"
                item.style.overflowY= item.contentEditable=='false' ? 'auto' : ''
                item.contentEditable= item.contentEditable=='false' ? 'true' : 'false'
                //set the caret after the last elemet when has at least one character
                if(item.textContent.length>0){
                    //create a range and selection  
                    this.caretInLastCharacter(item)
                }
                //click over over the paragraph editable to be able to edit and focus cursor directly to start to type
                item.focus();
            },

            /**
             * Handle and give time to finish the animation when display all information 
             * @param {Object} e - Event from element clicked
             * @param {String} type - Indicate if is list of list or list of tasks
             * @param {Boolean} clikedElementIsDate - Indicate if event was clicked was a date 
             */

            async showAllInformationChecker(e,type,clikedElementIsDate){
                if(!this.animationInProcess){
                    this.animationInProcess=true 
                     this.showAllInformation(e,type,clikedElementIsDate)
                    setTimeout(() => {
                        this.animationInProcess=false 
                    }, 350);
                }
            },

            /**
             * Display hidden element with additonal information of the item (date of creation and when was finished)
             * @param {Object} e - Event from element clicked
             * @param {String} type - Indicate if is list of list or list of tasks
             * @param {Boolean} clikedElementIsDate - Indicate if event was clicked was a date 
             * @param {Boolean} - new animation status
             */

            showAllInformation(e,type,clikedElementIsDate){
                //set a shortcut for the vue object father
                let father=type=="modalList" ? this.$parent.$parent : this.$parent;
                //get the item element and make sure that is the correct element
                let item= clikedElementIsDate ? e.target.previousElementSibling : e.target
                //get editor icon and data from the item 
                let editorIcon=item.parentElement.previousSibling.previousElementSibling
                let id=item.parentElement.id
                let index=item.parentElement.tabIndex
                // only can show extra information when editor is not active and is a task and his content is not empty
                if(editorIcon.className.match(/far fa-edit/g) && type!="modalList" && item.textContent.length>0){     
                    //if show the informatiom 
                    if(!this.show){
                        //first we show the full content
                        this.show=true
                        //second we adjist fit the height of the paragrah
                        item.classList.toggle("showItem")
                        //then we change the height 
                        item.parentElement.classList.add("efectText")
                    }else{
                        //first we remove the max height 
                        item.parentElement.classList.toggle("efectText")
                        //after we remove the date of creation
                        setTimeout(() => {
                            this.show=false
                            item.classList.toggle("showItem")
                        }, 350);
                    }
                //when you are in list of list we only can select a list if value is not empty and editor is not active
                }else if(editorIcon.className.match(/far fa-edit/g) && e.target.textContent.length>0){
                    //we check if editor has enable and if the user click over other list we save the changes
                    (father.itemInEdition==''|| father.itemInEdition.textContent.length>0) ? this.saveChangesList(type) : ''
                    //if some value is empty you can not select any list and you can select any list if you are saving data in database
                    if(this.checkEmptyValues() && father.notSavingChanges){
                        father.setList(id,index,item.textContent)
                    }
                }
            },

            /**
             * Check and limit the number of characters allowed in the item 
             * @param {Object} e - Event from element clicked
             * @param {String} type - Indicate if is list of list or list of tasks 
             */

            checkLenghtAndPaste(e,type){
                let plainTextTyped=e.target.textContent
                let maxLength=type=='modalList' ? 45 : 255
                let newAllowedString=plainTextTyped
                //Allways change the inner html to avoid html insertiom
                console.log(e.target.innerHTML)
                //check if length is correct and handle the number of char paste
                if(plainTextTyped.length>maxLength){
                    //resize the string and set in the new value in the current editor
                    plainTextTyped=plainTextTyped.slice(0,maxLength)
                    newAllowedString=this.previousString
                    e.target.innerHTML=e.inputType=='insertFromPaste' ? plainTextTyped : newAllowedString
                    //focus caret at the end
                    this.caretInLastCharacter(e.target)
                }

                if(e.inputType=='insertFromPaste'){
                    e.target.innerHTML=plainTextTyped
                    //focus caret at the end
                    this.caretInLastCharacter(e.target)
                }


                //use a regex to know if user insert a new paragraph 
                if(/<div>.*<\/div>/.test(e.target.innerHTML)){
                    // avoid add the paragraph
                    e.target.innerHTML=this.previousString
                    newAllowedString=this.previousString
                    //close automatically the editor
                    e.target.parentElement.previousElementSibling.click()
                }

                //save previous string
                this.previousString=newAllowedString
            },

            /**
             * Handle deletion of the current item from the current list and make sure that the change is apply in all variables 
             * @param {Object} e - Event from element clicked
             * @param {String} type - Indicate if is list of list or list of tasks  
             */

            async deleteItem(e,type){
                //get data from the current item 
                let father=type=="modalList" ? this.$parent.$parent : this.$parent;
                let index=e.target.previousElementSibling.tabIndex
                let id=e.target.previousElementSibling.id
                let currentList=type=='modalList' ? 'listOfLists' : 'listOfTasks'
                //only delete list/tasks when you are not in edit mode
                if(document.getElementsByClassName("editableBorder")[0]==undefined){
                    //open a alert modal and ask if the want delete the item 
                    if(e.target.previousElementSibling.children[0].textContent.length>0 ){
                        //save data from the item in variables of vue parent
                        father.currentId=parseFloat(id)
                        father.currentItem=index
                        father.messageAlert2= type=='modalList' ? 'the list?' : 'the task?'
                        //display alert to confirm delete the item 
                        father.showAlert=true
                        //change opacity of modal 
                        setTimeout(() => {document.getElementsByClassName("alertContainer")[0].style.opacity="1"}, 10);
                    }else if(father.displaySection!='example'){//deleted directly the item 
                        // remove the item from the list if are empty     
                        father[currentList].splice(index,1)
                        //refresh the items to avoid error in the displayed list
                        await father.refreshList(currentList,father[currentList].slice(0))
                        //check and modfy status if delete a item in list of task 
                        type!="modalList" ? father.checkAndModifyStatusList() : ''
                    }else{
                        //Delete a empty task in example list 
                        father.exampleData.splice(index,1)
                    }
                }
            },

            /** 
             * Handle the status of the current item saving the changes in the corresponding variables and set the correct styles 
             * @param {Object} e - Event from element clicked
             * @param {String} type - Indicate if is list of list or list of tasks 
             */

            async ChangeStatus(e,type){
                //get item 
                let selectedItem=e.target.previousElementSibling.previousElementSibling
                //Only can change the status in the list of task and this must not be empty
                if(type!='modalList' && selectedItem.children[0].textContent!=""){
                    //check star style and change it
                    this.starColor=this.starColor=='yellow' ? 'red' : 'yellow'
                    this.newStatus=this.newStatus=='finished' ? 'unfinished' : 'finished'
                    //get current id and index
                    let id=selectedItem.id
                    let index=selectedItem.tabIndex
                    //change the states create a date and time
                    let date=new Date()
                    let time=date.toLocaleTimeString().match(/\d+:\d{2}/g)[0]
                    let newDate= this.newStatus=='finished' ? date.toISOString().slice(0,10)+"|"+time : ''
                    if(this.$parent.displaySection=='list'){
                        //changes are save only for users
                        await this.$parent.saveChanges(index,id,"listOfTasks","finished",newDate)
                        await this.$parent.saveChanges(index,id,"listOfTasks","status",this.newStatus)
                        this.$parent.checkAndModifyStatusList()
                    }else{
                        //apply changes local for the example list 
                        this.$parent.exampleData[index].finished=newDate
                    }
                }
            }
        },
        components:{
            "button-component":button,
        }
    }
    
</script>

<style scoped>

.itemContainer{
    box-sizing: border-box;
    display: grid;
    grid-template-columns: 12.5% 65% 10% 12.5%;
    width: 100%;
}

.itemText{
    border-radius: 8px;
    border:2px solid transparent;
    box-sizing: border-box;
    cursor: pointer;
    display: block;
    font-family: 'Ubuntu', sans-serif;
    font-size: 1em;
    font-weight: 400;
    margin: 0;
    max-height: 65px;
    outline: none;
    overflow: hidden;
    padding: 5px 5px;
    text-align: justify;
    transition-duration: .35s;
    word-break: break-word;
}

.dateAndTime{
    box-sizing:unset;
    cursor:pointer;
    font-size: 88%;
    font-style: italic;
    padding-top:10px;
}

.popAnimation{
    animation-duration: 0.5s;
    animation-name: popup;
    animation-timing-function: ease-in;
}

.editable:empty:before{
    color:gray;
    content: attr(data-placeholder);
}

.noOutliner{
    border-radius: 8px;
    cursor: pointer;
    display: block;
    margin: 0;
    max-height: 65px;
    overflow: hidden;
    transition-duration: 0.35s;
    transition-timing-function: ease-in-out;
}

.noOutliner:focus{
    outline: none;
}

.efectText{
    max-height: 400px !important;
}

@keyframes popup{
    0%{font-size: 1.5em;}
    50%{font-size: 2em;}
    100%{font-size: 1.5em;}
}

.showItem{
    max-height: fit-content ;
}

.editableContainer{
    max-height: 90px;
}

.editableBorder{
    border:2px solid blue !important;
    cursor:text;
    margin:0 10px;
    max-height: 90px; 
}

</style>