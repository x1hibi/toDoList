<template>
        <div class="spinner"><i class="fas fa-spin" id="spinner">&#xf185;</i></div>
</template>

<script>
    export default {
        mounted() {
            //change color every .25 seconds
            let spinerInterval=setInterval(() => {
                if(document.getElementById("spinner")){
                    //get the spiner icon and change the color
                    let color=document.getElementById("spinner").style.color
                    document.getElementById("spinner").style.color= color=="red" ? "blue" : color=="blue" ? "yellow" : "red"
                }else{
                    //delete the interval 
                    clearInterval(spinerInterval)
                }
            }, 250);
        },
        props:{
            color:String,
            shadow:String,
            type:String,
        }
    }
    
</script>

<style scoped>

.spinner{
    background: white;
    display: grid;
    grid-area: 1/1/4/4;
    height: 100%;
    transition-duration: 0.5s;
    width: 100%;
    z-index: 3;
}

i{
    align-self: center;
    color:red;
    font-size: 100px;
    justify-self: center;
    transition-duration: 0.5s;
}

</style>