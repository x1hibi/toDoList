<template>
    <form>
        <div v-if="type=='login'">
            <input type="text" class="inputCute" v-model="usernameEmail" placeholder="Username / Email">
            <input type="password" class="inputCute" v-model="userPassword" placeholder="Password">
            <button type="button" class="buttonBlue" @click="login()">Login</button>
        </div>
        <div v-else>
            <input type="text" class="inputCute" v-model="username" placeholder="Username">
            <input type="text" class="inputCute" v-model="userEmail" placeholder="Email">
            <input type="password" class="inputCute" v-model="userPassword" placeholder="Password">
            <input type="password" class="inputCute" v-model="userPasswordConfirmation" placeholder="Confirm Password">
            <button type="button" class="buttonBlue" @click="register()">Sing Up</button>
        </div>
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
            type:String,
            positionY:String,
        }
        ,
        data(){
            return {
                debug:this.$parent.$parent.debug,
                usernameEmail:'',
                userPassword:'',
                username:'',
                userEmail:'',
                userPasswordConfirmation:'',
            }
        },

        methods:{
            editItem(){
                console.log("edit")
            },
            login(){
                console.log(this.usernameEmail,this.userPassword)
            },
            register(){
                console.log(this.username, this.userEmail,this.userPassword,this.userPasswordConfirmation)
            }
        }
    }
</script>


<style scoped>

form{
    overflow: auto;

}
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
    width: 95%;
    -webkit-appearance: none;
}

.inputCute:focus{
    box-shadow: 0 0 5px rgba(0, 0, 139, 0.5);
}
    


</style>