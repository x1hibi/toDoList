<template>
    <div class="list">
        <div class="headerList"><p class="title">{{headerList}}</p></div>
        <div id="bodyList" class="bodyList" >
            <section id="example" v-if="displaySection=='example'" class="fade">
                <item-component v-for="(item,index) in data" :key="index" :task="item.task" :status="item.status" :created="item.created" :finished="item.finished"></item-component>
            </section>
            <button @click="makePost()" v-if="false">make a petition</button>
            <login-component id="login" v-if="displaySection=='login'" type='login' class="fade"></login-component>
            <login-component id="register" v-if="displaySection=='register'" type='register' class="fade"></login-component>
        </div>

        <menu-component @click="login()" :option="loginIcon" :positionY="loginPosition" :toltip="loginToltip" disabled="true"></menu-component>
        <menu-component @click="register()" :option="registerIcon" :positionY="registerPosition" toltip="Sign up"></menu-component>
        <menu-component @click="showMenu()" :option="menuIcon" positionY="0" :toltip="menuToltip"></menu-component>

    </div>
</template>

<script>
    import item from "./ItemComponent"
    import menu from "./MenuComponent"
    import login from "./LoginComponent"
    export default {
        mounted() {
            this.$parent.debug("status","mounted")
            var isAndroid = navigator.userAgent.toLowerCase().indexOf("android") > -1; //&& ua.indexOf("mobile");
            if(isAndroid) {
                document.write('<meta name="viewport" content="width=device-width,height='+window.innerHeight+', initial-scale=1.0">');
            }
        },

        data(){
            return {
                debug:this.$parent.debug,
                test:"juan ",
                headerList:"Example List",
                loginPosition:"0",
                registerPosition:"0",
                menuDisplayed:false,
                menuIcon:'menu',
                loginIcon:'login',
                registerIcon:'register',
                menuToltip:'Display menu',
                loginToltip:"Sign in",
                currentSection:'example',
                displaySection:'example',
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
            register(){
                this.changeBody("register")
                this.changeTitle(this.currentSection=="register" ? 'Example List' : 'Register')
                this.loginToltip=this.displaySection=='example'  ? 'Example List' : 'Sign Up'
                this.registerIcon=this.displaySection=='example'  ? 'editor' : 'register'
                this.loginIcon='login'
            },
            login(){
                this.changeTitle(this.currentSection=="login" ? 'Example List' : 'Login')
                this.changeBody("login")

                this.loginToltip=this.displaySection=='example' ? 'Example List' : 'Sign in'
                this.loginIcon=this.displaySection=='example'  ? 'editor' : 'login'
                this.registerIcon='register'
            },
            changeTitle(newTitle){
                //we set opacity to 0 and after change the title and set again the opacity
                document.getElementsByClassName("title")[0].style.opacity=0
                setTimeout(() => {
                    this.headerList=newTitle
                    document.getElementsByClassName("title")[0].style.opacity=1
                }, 250);
                
            },
            changeBody(idToDisplay){
                console.log(this.currentSection)
                this.currentSection=idToDisplay

                setTimeout(() => {
                    
                }, 250);  

                //we set opacity to 0 and after change the title and set again the opacity
                /*
                let displayed=document.getElementById(idToDisplay)

                document.getElementById("bodyList").style.overflowY=this.displaySection!='example' ? 'scroll' : 'hidden';
                document.getElementById("bodyList").parentElement.style.height=(this.displaySection=="example" || this.displaySection=="list") ? 'fit-content' : '100%';
                this.debug('changebody','before if')
                this.debug('dis',idToDisplay)

                document.getElementById(this.currentSection).style.opacity=0

                this.debug('changebody','afer if')

                this.menuDisplayed=!this.menuDisplayed
                this.menuIcon=this.menuDisplayed ? 'close' : 'menu'
                this.loginPosition="0"
                this.registerPosition="0"
                setTimeout(() => {
                    this.displaySection= this.displaySection==this.currentSection ? idToDisplay : this.currentSection   
                    this.currentSection= idToDisplay
                }, 250);                                                                                                          
                console.log(this.displaySection)
                */
            },
            animationHandler(){
                
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
    grid-template-rows: auto 1fr;
    grid-area: 2/2/3/3;
    height: 100%;
}


.headerList{
    background: linear-gradient(to right,rgb(113, 36, 214),rgb(126, 52, 224),rgb(141, 68, 236),rgb(156, 93, 240));
    border-radius: 15px 15px 0 0;
    display: grid;
    grid-area: 1/1/2/2;
    width: 100%;
}

.title{
    align-self: center;
    color:rgb(255, 255, 255);
    font-size: 2.25em;
    font-family: 'Ubuntu', sans-serif;
    font-weight: 300;
    justify-self: center;
    margin: 0;
    padding: 20px 0;
    text-shadow: 0 0 3px rgb(75, 8, 8);
    transition:opacity 0.25s ease-in;
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


.fade{
    animation-name: fadeIn;
    animation-duration: 0.25s;
    animation-timing-function:ease-in-out ;
    transition-duration: 0.5s;

}

@keyframes fadeIn{
    0%{opacity: 0;}
    100%{opacity: 1;}
}


</style>