<template>
        <i @click="$emit('click',$event)"  :class="[option=='menu' ? 'fas fa-stream buttonFixed' : option=='login' ? 'fas fa-user buttonFixed' : option=='register' ? 'fa fa-user-plus buttonFixed' : option=='editor' ? 'fas fa-pen buttonFixed' : option=='logout' ? 'fas fa-user-alt-slash buttonFixed' : option=='list' ? 'fa fa-list-ul buttonFixed' : 'fas fa-times buttonFixed' ]" :style="{bottom: `${(50*this.positionY)+15*(this.positionY)+10}px`}" :title="toltip"></i>
</template>

<script>
    export default {
        props:{
            option:String,
            positionY:String,
            toltip:String,
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
    height: 50px;
    justify-content: center;
    outline: none;
    padding: 0;
    position: fixed;
    right: 10px;
    transition-duration: 0.35s;
    transition-timing-function: ease-in-out;
    width: 50px;
}

.buttonFixed:hover{
    text-shadow: 0 0 10px rgb(176, 176, 235);
}

</style>