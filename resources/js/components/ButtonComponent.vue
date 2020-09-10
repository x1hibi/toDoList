<template>
        <i @click="$emit('click',$event)" :class="[ type == 'editorOff' ? 'far fa-edit' : type == 'editorOn' ? 'fas fa-edit popAnimation' : type == 'delete' ? 'fas fa-trash-alt' :  type == 'unfinished' ? 'far fa-star' : 'fas fa-star popAnimation borderShadow' ]" :style="{color:color,textShadow: shadow}"></i>
</template>

<script>
    export default {
        props:{
            color:String,
            shadow:String,
            type:String,
        }
    }
    
</script>

<style scoped>

i{
    align-self: center;
    cursor: pointer;
    font-size: 25px;
    justify-self: center;
    transition-duration: 0.5s;
}

i:hover{
    text-shadow: 0 0 1.5px rgb(30, 9, 255,0.5);
}

.borderShadow{
    text-shadow: 0 0 1.5px rgb(30, 9, 255,0.5);
}

</style>