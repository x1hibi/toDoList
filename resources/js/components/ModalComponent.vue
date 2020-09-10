<template>
    <div class="modalContainer">
        <div class="modal">
            <div class="headerList" style="padding:20px 0">
                <p class="title">{{title}}</p>
            </div>
            <div class="modalBody">
                <section id="ListOfLists" class="fade">
                    <item-component v-for="(item,index) in listOfLists" :key="index" :index="index" :id="item.list_id" :value="item.list_name" :status="item.status" :created="item.created" :finished="item.finished" type="modalList"></item-component>
                </section > 
            </div>
            <div class="buttonContainer" v-if="notTyped">
                <button type="button" class="buttonBlue fas fa-plus" @click="addList()"></button>
                <button type="button" class="buttonBlue far fa-save" @click="saveChanges()"><i class="fas fa-spin" style="display:none">&#xf1ce;</i></button>
            </div>
        </div>
    </div>
</template>

<script>
import item from './ItemComponent'
export default {
        props:{
            notTyped:Boolean,
            title:String,
        },
        data(){
            return {
                listOfLists:this.$parent.listOfLists,
            }
        },
        methods:{
            addList(){
                this.$parent.addItemToList(false)
            },
            saveChanges(){
                this.$parent.saveChangesInDB('/api/saveModificationsToUserLists','modifiedLists','deletedLists','listOfLists')
            }
        },
        components:{
            'item-component':item,
        }
    }
</script>

<style scoped>

.modalContainer{
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
    z-index: 2;
}

.modal {
    -webkit-border-radius: 18px;
    background-size: cover;
    background: linear-gradient(cyan,rgb(238, 227, 227),rgb(165, 206, 224));
    border-radius: 18px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 85);
    box-sizing: content-box;
    font-family: 'Open Sans', sans-serif;
    height: 90%;
    margin: 0;
    overflow-x: hidden;
    overflow-y: hidden;
    scrollbar-width: thin;
    transition: all .3s ease;
    width: 85%;
}

.modalBody{
    font-family: 'Ubuntu', sans-serif;
    font-weight: 600 !important;
    height:calc(95% - 125px);
    margin: 5px;
    overflow-y: auto;
}

.buttonContainer{
    display: grid;
    grid-template-columns: 1fr 1fr ;
    height: 10%;
}

.buttonContainer button{
    align-self: center;
    box-shadow: 0 0 5px 0px gray;
    height:50px;
    justify-self: center;
    margin-bottom: 10px;
    padding:0;
    width:50px;
}

@media (min-width: 769px){
    .modal{
        width: 45%;
    }
}

</style>