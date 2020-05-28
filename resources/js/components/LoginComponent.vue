<template>
    <form>
        <input class="inputCute" placeholder="Username / Email">
        <input class="inputCute" placeholder="Password">
        <button class="buttonBlue">Login</button>
    </form>
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


.inputCute{
    transition-duration: 0.35s;
    border-radius: 25px;
    border: 5px outset rgb(156, 93, 240);
    color: #6a6a6a;
    display: block;
    margin: auto;
    margin-bottom: 1em;
    outline: none;
    padding: 0;
    font-family: 'Ubuntu', sans-serif;
    font-size: 1.1em;
    font-weight: 400;
    text-align: center;
    height: 40px;
    width: 70%;
    -webkit-appearance: none;
}

.inputCute:focus{
    box-shadow: 0 0 5px rgba(0, 0, 139, 0.5);
}
    


</style>