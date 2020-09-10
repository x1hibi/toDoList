window.Vue = require('vue');

import spinner from "./components/SpinnerComponent";
import listContainer from "./components/ListComponent" 

//Create a vue object root  
const app = new Vue({
    el: '#listApp',
    data :{
        spinner:true,
    },
    components:{
        'spinner-component':spinner,
        'list-component':listContainer,
    }
    
});
