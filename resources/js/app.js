window.Vue = require('vue');

import example from "./components/ExampleComponent";
import listContainer from "./components/ListComponent" 

const app = new Vue({
    el: '#listApp',
    components:{
        'test-component':example,
        'list-component':listContainer,
    },
    methods:{
        debug(label,message){
            let Label=label[0].toUpperCase() + label.slice(1)
            return typeof(message)=="string" ? console.log(`%c ${Label}: %c${message}`,"color:yellow","color:cyan") : console.log(`%c ${Label}:`,"color:white",message)
        }
    }

});
