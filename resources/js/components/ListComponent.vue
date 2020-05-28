<template>
    <div class="list">
        <div class="headerList"><p class="title">{{headerList}}</p></div>
        <div id="bodyList" class="bodyList" >
            <section v-if="displayList">
                <item-component v-for="(item,index) in data" :key="index" :task="item.task" :status="item.status" :created="item.created" :finished="item.finished"></item-component>
            </section>
            <button @click="makePost()" v-if="false">make a petition</button>
            <login-component v-if="displayLogin"></login-component>
        </div>

        <menu-component @click="login()"  :option="loginIcon" :positionY="loginPosition" :toltip="loginToltip"></menu-component>
        <menu-component option="register" :positionY="registerPosition" toltip="Sign up"></menu-component>
        <menu-component @click="showMenu()"  :option="menuIcon" positionY="0" :toltip="menuToltip"></menu-component>

    </div>
</template>

<script>
    import item from "./ItemComponent"
    import menu from "./MenuComponent"
    import login from "./LoginComponent"
    export default {
        mounted() {
            this.$parent.debug("status","mounted")
        },

        data(){
            return {
                test:"juan ",
                headerList:"Example List",
                loginPosition:"0",
                registerPosition:"0",
                menuDisplayed:false,
                menuIcon:'menu',
                loginIcon:'login',
                menuToltip:'Display menu',
                loginToltip:"Sign in",
                displayLogin:false,
                displayList:true,
                data:[{
                    task:'Scary Movie',
                    status:'finished',
                    created:'2019-06-91',
                    finished:'2020-09-12'
                },
                {
                    task:'Doris',
                    status:'finished',
                    created:'2019-06-91',
                    finished:'2020-09-12'
                },
                {
                    task:'nemo',
                    status:'finished',
                    created:'2019-06-91',
                    finished:'2020-09-12'
                },
                {
                    task:'doris2',
                    status:'finished',
                    created:'2019-06-91',
                    finished:'2020-09-12'
                }]
            }
        },

        methods:{
            newTest(){
                console.log(this.test)
            },
            showMenu(){
                this.menuDisplayed=!this.menuDisplayed
                this.menuIcon=this.menuDisplayed ? 'close' : 'menu'
                this.menuToltip=this.menuDisplayed ? 'Close menu' : 'Display menu'
                this.loginPosition= this.menuDisplayed ? "1" : "0"
                this.registerPosition=this.menuDisplayed ? "2" : "0"
            },
            login(){
                document.getElementById("bodyList").classList.toggle("loginBody")
                document.getElementById("bodyList").parentElement.classList.toggle("listLogin")
                this.displayLogin=!this.displayLogin
                this.displayList=!this.displayList
                this.headerList=this.displayLogin ? 'Sign in' : 'Example List'
                this.menuDisplayed=!this.menuDisplayed
                this.menuIcon=this.menuDisplayed ? 'close' : 'menu'
                this.loginPosition="0"
                this.registerPosition="0"
                this.loginToltip=this.displayLogin ? 'Example List' : 'Sign in'
                this.loginIcon=this.displayLogin ? 'editor' : 'login'
            },
            makePost(){
                console.log("start a post")
                
                /*
                axios.post('/api/insert',{username:"dam",email:'damiis298@gmail.com',password:"patitas"}).then(
                    response=>{
                        console.log(response.data)
                    }
                ).catch(error=>{console.log(error)})
                
               axios.get('/api/select').then(
                    response=>{
                        console.log(response)
                    }
                ).catch(error=>{console.log(error)})
                
               axios.put('/api/update',{id:2,username:"dam21",email:'22damiis298@gmail.com',password:"2  patitas"}).then(
                    response=>{
                        console.log(response.data)
                    }
                ).catch(error=>{console.log(error)})
                axios.delete('/api/delete',{params:{id:4}}).then(
                    response=>{
                        console.log(response.data)
                    }
                ).catch(error=>{console.log(error)})
              
                
               axios.get('/api/select2').then(
                    response=>{
                        console.log(response)
                    }
                ).catch(error=>{console.log(error)})

                axios.get('/api/select2',{params:{password:"patitas"}}).then(
                    response=>{
                        console.log(response)
                    }
                ).catch(error=>{console.log(error)})



                axios.get('/api/check',{params:{password:"patitas2"}}).then(
                    response=>{
                        console.log(response)
                    }
                ).catch(error=>{console.log(error)})
                
             
                */

                axios.post('/api/insert',{username:"dam21",email:'d12amiis298@gmail.com',password:"patitas"}).then(
                    response=>{
                        console.log(response.data)
                    }
                ).catch(error=>{console.log(error)})
                
                
                
            }
        },
        components:{
            "item-component":item,
            "menu-component":menu,
            "login-component":login,
        }
    }
</script>


<style scoped>

.list{
    background: blue;
    border-radius: 15px;
    box-shadow: 5px 5px 10px 0 rgba(255, 182, 193, 0.5);
    display: grid;
    grid-template-rows: 15% 85%;
    grid-area: 2/2/3/3;
    height: 100%;
}

.headerList{
    background: linear-gradient(to right,rgb(113, 36, 214),rgb(126, 52, 224),rgb(141, 68, 236),rgb(156, 93, 240));
    border-radius: 15px 15px 0 0;
    display: grid;
    grid-area: 1/1/2/2;
    height: 100%;
    width: 100%;
}

.title{
    align-self: center;
    color:rgb(255, 255, 255);
    font-size: 2.5em;
    font-family: 'Ubuntu', sans-serif;
    font-weight: 300;
    margin: 0;
    text-shadow: 0 0 3px rgb(75, 8, 8);
    justify-self: center;
    width:fit-content;
}

.bodyList{
    background:linear-gradient(to top, rgb(235, 232, 232), whitesmoke,rgb(240, 232, 232));
    border:1px solid red;
    border-radius: 0 0 15px 15px;
    box-sizing: border-box;
    grid-area: 2/1/3/2;
    height: 100%;
    padding:5% 2.5%;
    overflow-y: scroll;
}

.loginBody{
    border-radius: 15px;
    overflow-y: hidden;
    height: fit-content;
}

.listLogin{
    height: fit-content;
}

</style>