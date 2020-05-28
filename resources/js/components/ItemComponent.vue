<template>
    <div>
        <div class="itemContainer">
        <button-component :type="editor" color="gray" @click="editItem($event)" ></button-component>
        <div>
            <p class="itemText" contentEditable="false" @click="showItem($event)" @input="checkLenght($event)" title="Show All Inoformation">{{task}}</p>
            <p class="itemText" v-if="show"><i>{{'Created: '+created}} <br/> {{'Finished: '+finished}}</i></p>
        </div>
        <button-component type="delete" color="black"  @click="deleteItem()" ></button-component>
        <button-component :type="newStatus" :color="starColor" :shadow="shadow" @click="checkItem($event)"></button-component>
        </div>
        <hr/>
    </div>
</template>

<script>
import button from "./ButtonComponent"
    export default {
        mounted() {
            this.debug("status","mounted item")
        },
        props:{
            task:String,
            status:String,
            created:String,
            finished:String
        },

        data(){
            return {
                'debug':this.$parent.$parent.debug,
                'editor':'editorOff',
                'shadow':this.status=='finished' ? '0 0 1.5px rgb(30, 9, 255,0.5)' : '',
                'starColor':this.status=='finished' ? 'yellow' : 'red',
                'show':false,
                'newStatus':this.status
            }
        },

        methods:{
            editItem(e){
                this.show=false
                let item=e.target.nextElementSibling.children[0]
                item.className= item.className.match(/editableBorder/g) ? 'itemText' : 'itemText editableBorder'
                this.debug("list",item.classList)
                this.debug("editor",this.editor)
                this.editor=item.contentEditable=='false' ? 'editorOn' : 'editorOff'
                item.style.overflowY= item.contentEditable=='false' ? 'scroll' : ''
                item.contentEditable= item.contentEditable=='false' ? 'true' : 'false'

            },
            showItem(e){
                let item=e.target
                if(this.editor=="editorOff"){
                    item.classList.toggle("showItem")
                    this.show=!this.show
                }
                this.debug("show","yes")
            },
            checkLenght(e){
                let value=e.target.textContent
                console.log(value)
            },
            deleteItem(e){
                console.log("delete")
            },
            checkItem(e){
                console.log("check")
                //this.shadow=this.shadow=="" ? '0 0 1.5px rgb(30, 9, 255,0.5)' : '';
                this.starColor=this.starColor=='yellow' ? 'red' : 'yellow'
                this.newStatus=this.newStatus=='finished' ? 'finish' : 'finished'
                      
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
    grid-template-columns: 10% 70% 10% 10%;
    width: 100%;
}

.itemText{
    border:1px solid transparent;
    border-radius: 8px;
    display: block;
    cursor: pointer;
    font-family: 'Ubuntu', sans-serif;
    font-size: 1em;
    font-weight: 400;
    grid-area: 1/2/2/3;
    hyphens:auto;
    margin: 0;
    max-height: 45px;
    padding: 10px;
    text-align: justify;
    transition-duration: 0.5s;
    outline: none;
    overflow: hidden;
}

.popAnimation{
    animation-name: popup;
    animation-duration: 0.35s;
    animation-timing-function: ease-in;
}

@keyframes popup{
    0%{font-size: 1.5em;}
    50%{font-size: 2em;}
    100%{font-size: 1.5em;}
}

.showItem{
    max-height: fit-content ;
}

.editableBorder{
    border:1px solid blue;
    cursor:text;
    margin:0 2.5px;
    max-height: 90px;
}


</style>