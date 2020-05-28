<template>
        <i @click="$emit('click',$event)"  :class="[option=='menu' ? 'fas fa-stream buttonFixed' : option=='login' ? 'fas fa-user buttonFixed' : option=='register' ? 'fa fa-user-plus buttonFixed' : option=='editor' ? 'fas fa-pen buttonFixed' : 'fas fa-times buttonFixed' ]" :style="{bottom: `${(50*this.positionY)+15*(this.positionY)+10}px`}" :title="toltip"></i>
</template>

<script>
    export default {
        mounted() {
            this.debug("status","mounted menu")
        },
        props:{
            option:String,
            toltip:String,
            positionY:String,
        }
        ,
        data(){
            return {
                'debug':this.$parent.$parent.debug,
            }
        },

        methods:{
            editItem(){
                console.log("edit")
            }
        }
    }
</script>


<style scoped>


.buttonFixed{
    align-items: center;
    background: linear-gradient(to right,rgb(113, 36, 214),rgb(126, 52, 224),rgb(141, 68, 236),rgb(156, 93, 240));
    border-radius: 35px;
    box-shadow: 2px 2px 8px lightgrey;
    color: white;
    cursor: pointer;
    display: grid;
    font-size: 25px;
    outline: none;
    padding: 0;
    position: fixed;
    justify-content: center;
    right: 10px;
    transition-duration: 0.35s;
    transition-timing-function: ease-in-out;
    height: 50px;
    width: 50px;
}

.buttonFixed:hover{
    text-shadow: 0 0 10px rgb(176, 176, 235);
}


</style>