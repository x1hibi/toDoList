<template>
    <form @submit.prevent="type=='login' ? login() : register()">
        <div v-if="type=='login'">
            <input type="text" class="inputCute" @input="inputChecker($event,'usernameEmail',40,1,4)" placeholder="Username / Email" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
            <span>{{messageUsernameEmail}}</span>
            <input type="password" class="inputCute" @input="inputChecker($event,'password',16,2,2)" placeholder="Password" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
            <span>{{messagePassword}}</span>
            <button type="submit" class="buttonBlue" @click="login()"><i>Sing In</i></button>
        </div>
        <div v-else>
            <input type="text" class="inputCute" @input="inputChecker($event,'username',15,0,0)" placeholder="Username" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
            <span>{{messageUsername}}</span>
            <input type="text" class="inputCute" @input="inputChecker($event,'userEmail',40,1,1)" placeholder="Email" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
            <span>{{messageEmail}}</span>
            <input type="password" class="inputCute" @input="inputChecker($event,'userPassword',16,2,2)" placeholder="Password" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
            <span>{{messagePassword}}</span>
            <input type="password" class="inputCute" @input="inputChecker($event,'userPasswordConfirmation',16,2,3)" placeholder="Confirm Password" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false">
            <span>{{messagePasswordConfirmation}}</span>
            <button type="submit" class="buttonBlue" @click="register()"><i>Sing Up</i></button>
        </div>
    </form>
</template>

<script>
    export default {
        props:{
            option:String,
            positionY:String,
            toltip:String,
            type:String,
        },
        data(){
            return {
                loginSuccessfully:false,
                messageEmail:"Use format example@domain.com",
                messagePassword:"Must contain 8-16 alphanumeric or symbols",
                messagePasswordConfirmation:"Confirm the password",
                messageUsername:"Starts with a letter, contain 6-16 alphanumeric",
                messageUsernameEmail:"Must contain at least 6 letter-number or (.-_ @)",
                password:"",
                passwordChecked:false,
                registerSuccesfully:false,
                userEmail:'',
                userEmailChecked:false,
                username:'',
                usernameChecked:false,
                usernameEmail:'',
                usernameEmailChecked:false,
                userPassword:'',
                userPasswordChecked:false,
                userPasswordConfirmation:'',
                userPasswordConfirmationChecked:false,
            }
        },
        methods:{
            
            /**
             * disable or ativate inputs,buttons and dispaly none the fixed buttons and set a spinner
             * @param {Boolean} disableAndHide - Especify if disable elements or active
             * @param {String} text - Text set in button 
             */

            disableElements(disableAndHide,text="&#xf1ce;"){
                //get all inputs, floating buttons and buttons 
                let inputsButtons=[...document.getElementsByTagName("input"),...document.getElementsByTagName("button")]
                let fixedButtons=[...document.getElementsByClassName("buttonFixed")]
                let buttonSpinner=inputsButtons[inputsButtons.length-1].children[0]
                //disable all inputs and buttons
                inputsButtons.forEach(input=>{input.disabled=disableAndHide})
                fixedButtons.forEach(button=>{button.style.display= disableAndHide ? "none" : "grid"})
                //change button style to see process animation and change text
                buttonSpinner.className= disableAndHide ? (buttonSpinner.style.textShadow="0 0 2.5px white",'fas fa-spin') : (buttonSpinner.style.textShadow="",'')
                buttonSpinner.innerHTML=text
            },  

            /**
             * This function decode a string with the user data with a random key to convert char code to string
             * @param {String} userData - User credentials encrypted for login session
             * @param {String} randomKey - Key to decode the user data
             */

            decode(userData,randomKey){
                let arr=[]
                let random=randomKey.split("")
                //iterate every character and decode
                userData.forEach((char,index)=>{
                    arr[index]=String.fromCharCode(char-random[index])
                })
                //save user data encrypted 
                this.$parent.userData=arr.join("").split("-")
            },
            
            /**
             * This function encode the user information to save as a cookie for login automatically 
             * @param {(String|Number)} id - User id
             * @param {String} username - Username of user
             */
            
            encode(id,username){
                let cookie=""
                let random=""
                let preCookie=`${id}-${username}`
                let prearrayCookie=preCookie.split("")
                // encrypt every character
                prearrayCookie.forEach(char => {
                    let randomNumner=Math.round(Math.random()*9);
                    cookie=cookie.concat(`${char.charCodeAt()+randomNumner}.`)
                    random=random.concat(`${randomNumner}`)
                });
                // set encrypted user data and key in a cookie
                this.$parent.setAndDeleteEncryptCookie("set",cookie.slice(0,cookie.length-1),random)
                //close floating buttons
                document.getElementsByClassName("fa-pen")[0].click()  
            },

            /**
             * Handle user login, make request to check username/email and password to make auth
             */

            async login(){
                //to avoid multiple request disable all buttons
                this.disableElements(true)
                //get all inputs
                let inputs=[...document.getElementsByTagName("input")]
                let inputsChecks=[this.usernameEmailChecked,this.passwordChecked]
                //if all input are checked means that all complies with its syntax start request 
                //check the status of inputs 
                if(inputsChecks.every(checked=>checked==true)){
                    //make a asyncronous petition to make a insert into the register table
                    await axios.get('/api/login',{params:{usernameEmail:this.usernameEmail,password:this.password}}).then(
                        response=>{
                            //check response to see if data is incorrect and set message error and red border
                            if(response.data[0].match(/incorrect/gi)){
                                response.data[0].match(/username/g) ? (inputs[0].style.borderColor="red",inputs[0].nextElementSibling.style.opacity="1",inputs[0].nextElementSibling.textContent="Incorrect username or email") : ''
                                response.data[0].match(/password/g) ? (inputs[1].style.borderColor="red",inputs[1].nextElementSibling.style.opacity="1",inputs[1].nextElementSibling.textContent="Incorrect password") : ''
                                //print the error
                                response.data[2]!='' ? this.$parent.debug("error",response.data[2].errorInfo[2]) : ''
                            }else{
                                //if are correct we  decode the returned data and encode data to save in cookies
                                this.decode(response.data[1][0],response.data[1][1])
                                this.encode(this.$parent.userData[0],this.$parent.userData[1],"set first cookie")
                                //activate spinner and reload page
                                this.$parent.$parent.spinner=true;
                                location.reload()
                           }
                        }
                    ).catch(error=>{console.log(error)})
                }else{
                    //we set a pink border in the inputs not checked
                    inputs.forEach((input,index)=>{
                        input.style.borderColor=inputsChecks[index] ? 'rgb(156, 93, 240)' : 'deeppink'
                    })
                }
                //activate buttons 
                this.disableElements(false,"Sing In")
            },

            /**
             * Handle register of user, check that all fields are correct and sent a request to save data of the new user
             */

            async register(){
                //disable buttons for multiple request
                this.disableElements(true)
                //check the status of all data 
                let inputs=[...document.getElementsByTagName("input")]
                let inputsChecks=[this.usernameChecked,this.userEmailChecked,this.userPasswordChecked,this.userPasswordConfirmationChecked]
                //if all input are checked means that all complies with its syntax
                if(inputsChecks.every(checked=>checked==true)){
                    //make a asyncronous petition to make a insert into the register table
                    await axios.post('/api/register',{username:this.username,email:this.userEmail,password:this.userPassword}).then(
                        response=>{
                            //if the data was save successfully we define a success register ofr new uses
                            if(response.data.match(/new user/g)){
                                this.registerSuccesfully=true
                            }else{
                                // when data was not register in database we set a message with the error and set a red borde 
                                let inputs=document.getElementsByTagName("input")
                                response.data.match(/username/g) ? (inputs[0].style.borderColor="red",inputs[0].nextElementSibling.style.opacity="1",inputs[0].nextElementSibling.textContent="Username is already registered") : ''
                                response.data.match(/email/g) ? (inputs[1].style.borderColor="red",inputs[1].nextElementSibling.style.opacity="1",inputs[1].nextElementSibling.textContent="Email is already registered") : ''
                            }
                        }
                    ).catch(error=>{console.log(error)})
                }else{
                    //we set a pink border in the inputs not checked
                    inputs.forEach((input,index)=>{input.style.borderColor=inputsChecks[index] ? 'rgb(156, 93, 240)' : 'deeppink'})
                }
                //activate all buttons
                this.disableElements(false,"Sing Up")
 
                if(this.registerSuccesfully){
                    //redirect to make login and close menu
                    document.getElementsByClassName("fa-user")[0].click()
                    setTimeout(() => {
                        document.getElementsByClassName("fa-times")[0].click()
                        this.registerSuccesfully=false
                    }, 50);
                }
            },

            /**
             * We evaluate every character typed in input and check if the character is allowed or not
             * @param {Object} e - event from element 
             * @param {String} currentInput - Indicate type of input
             * @param {Number} maxLength - Max lenght allowed in input
             * @param {Number} symbolIndex - Index for the regex to use to check the symbols allowed
             * @param {Number} syntaxIndex - Index for the regex to use to chekc the syntax for the current input
             */

            inputChecker(e,currentInput,maxLength,symbolIndex,syntaxIndex,passArray=[]){
                //we set a border blue if is red previusly
                e.target.style.borderColor=e.target.style.borderColor=="deeppink" ? "rgb(155, 95, 240)" : e.target.style.borderColor
                //set a limit of characters
                e.target.value.length>maxLength ? e.target.value=this[currentInput] : this[currentInput]=e.target.value
                //we asign direvtly the value because model assign don't
                this.userPassword.split("").forEach((letter,index)=>{passArray[index]=letter.match(/\w/g) ? letter : `\\${letter}` })
                //array with regex with the allowed characters
                let allowedSymbols=[/\w/g,/[\w@\-\_\.]/g,/[^ üîøåéñáéíóú]/g] // [0] regular expresion to check is a number or a letter,[2] check is a number, letter or a symbol avalible 
                //array with regex with the rules for each input
                let inputSyntax=[
                    /^[a-zA-Z]\w{5,14}/g,// the regex is used to match usernames with 6-15 alphanumeric characters and should begin with a letter
                    /^[a-zA-Z](([\w\-\_]+(?<=[\w\-\_])\.?)+)?(?<=[\w\-\_])@\w{3,}\.\w{2,4}(\.\w{2,4})?/g, //the regex is used to match email with the syntax someone@domain.com
                    /[^ üîøåéñáéíóú]{8,16}/g,// the regex is used to match password with 8-16 characters except for accented letters and spaces
                    new RegExp(`${this.userPassword.length>=8 ? passArray.join("") : ' '}`,"g"), // the regex is used to match usernamePassword with usernamePasswordConfirmation
                    /^[a-zA-Z](([\w\-\_]+(?<=[\w\-\_])\.?)+)?(?<=[\w\-\_])@\w{3,}\.\w{2,4}(\.\w{2,4})?|^[a-zA-Z]\w{5,14}/g // this evaluate if the string match with a username or a valid email
                ]
                //set a message below the input to say to user the correct syntax
                let message=e.target.nextElementSibling
                message.textContent=currentInput=="username" ? this.messageUsername : currentInput=="userEmail" ? this.messageEmail : message.textContent
                let inputArray=this[currentInput].split("")
                //we check every typed character
                for(let i=0;i<inputArray.length;i++){
                    if(inputArray[i].match(allowedSymbols[symbolIndex])){
                        continue
                    }else{
                        //we check if the character is type at first, middle or at the end
                        let newValueInput=  i==0 ? this[currentInput].slice(1):
                                            i==inputArray.length-1 ? this[currentInput].slice(0,i):   
                                            this[currentInput].slice(0,i)+this[currentInput].slice(i+1);
                        //we set in the variable and the current input the new value if find a not allowed character
                        this[currentInput]=newValueInput
                        e.target.value=newValueInput
                        break
                    }   
                }
                
                //this check if the rules of each input is follow check as correct, change the color of border and message opacity
                (this[currentInput].match(inputSyntax[syntaxIndex])!=null && this[currentInput].match(inputSyntax[syntaxIndex])[0].length==this[currentInput].length) ?  
                    (this[currentInput+"Checked"]=true,e.target.style.borderColor="rgb(150, 80, 240)",message.style.opacity='0') : 
                    (this[currentInput+"Checked"]=false,e.target.style.borderColor="rgb(155, 150, 240)",message.style.opacity='1')
                
                //if the input is empty we remove the message
                e.target.value.length==0 ? message.style.opacity='0' : ''

                //this if the user change the password after set a confirm password
                if(currentInput=="userPassword" && this.userPasswordConfirmation.length>1){
                    let confirmPassword=e.target.nextElementSibling.nextElementSibling
                    this.userPasswordConfirmationChecked=this.userPassword==this.userPasswordConfirmation ? (confirmPassword.style.borderColor="rgb(150, 80, 240)",confirmPassword.nextElementSibling.style.opacity=0,true) : (confirmPassword.style.borderColor="rgb(155, 150, 240)",confirmPassword.nextElementSibling.style.opacity=1,false)
                }
            }

        }
    }

</script>

<style scoped>

form{
    align-self: center;
    height: fit-content;
    padding: 5px 0;
}

.inputCute{
    -webkit-appearance: none;
    border-radius: 25px;
    border: 5px outset rgb(155, 150, 240);
    color: #6a6a6a;
    display: block;
    font-family: 'Ubuntu', sans-serif;
    font-size: 1.1em;
    font-weight: 400;
    height: 40px;
    margin: auto;
    outline: none;
    padding: 0;
    text-align: center;
    transition-duration: 0.35s;
    transition-duration: 0.5s;
    width: 95%;
}

.inputCute:focus{
    box-shadow: 0 0 5px rgba(0, 0, 139, 0.5);
}
    
span{
    color: rgb(34, 34, 83);
    display: block;
    font-family: 'Ubuntu', sans-serif;
    font-size: 0.75em;
    margin: auto;
    opacity: 0;
    padding: 5px 0;
    text-align: center;
    transition-duration: 0.5s;
    width: fit-content;
}

</style>