<template>
        <i @click="$emit('click',$event)" :class="[ type == 'editorOff' ? 'far fa-edit' : type == 'editorOn' ? 'fas fa-edit popAnimation' : type == 'delete' ? 'fas fa-trash-alt' :  type == 'finish' ? 'far fa-star' : 'fas fa-star popAnimation' ]" :style="{color:color,textShadow: shadow}"></i>
</template>

<script>
    export default {
        mounted() {
            this.debug("status","mounted button")
        },
        props:{
            type:String,
            color:String,
            shadow:String,
        }
        ,
        data(){
            return {
                'debug':this.$parent.$parent.$parent.debug,
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


</style>