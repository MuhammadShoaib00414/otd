/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

 require('./bootstrap');

 window.Vue = require('vue').default;
 import App from './modules/App.vue';
 import VueRouter from 'vue-router';
 import VueAxios from 'vue-axios';
 import axios from 'axios';
 import {routes} from './router/index';
 import MobileLinks from './modules/_components/MobileLinks'
 import NavBar from './modules/_components/NavBar'
import Vue from 'vue'

 Vue.use(VueRouter);
 Vue.use(VueAxios, axios);
 Vue.component('mobile-links', MobileLinks)
Vue.component('nav-bar', NavBar)
 
 const router = new VueRouter({
     routes: routes
 });
 router.beforeEach((to, from, next) => {
    if (!Vue.prototype.$user || !Vue.prototype.$settings) {
        axios.all([
                axios.get('/api/user'),
                axios.get('/api/settings')
            ]).then(axios.spread((response1, response2) => {
                Vue.prototype.$user = response1.data.user;
                Vue.prototype.$usersGroups = response1.data.groups;
                Vue.prototype.$settings = response2.data;
                
                next();
             }))
    } else {
        next()
    }
})

Vue.use(require('vue-moment'));
var scriptName = document.querySelector('script[src*="main.js"]').src;
if (localStorage.getItem('last-loaded-script') && localStorage.getItem('last-loaded-script') != scriptName)
    localStorage.clear()
localStorage.setItem('last-loaded-script', scriptName);

var requestsToMake = [axios.get('/api/user')];

if (localStorage.getItem('settings')) {
    Vue.prototype.$settings = JSON.parse(localStorage.getItem('settings'));
    setTimeout(() => {
        axios.get('/api/settings')
             .then((response) => {
                Vue.prototype.$settings = response.data;
                localStorage.setItem('settings', JSON.stringify(response.data))
             })
    }, 3000)
} else {
    requestsToMake.push(axios.get('/api/settings'));
}

if (localStorage.getItem('localization') && JSON.parse(localStorage.getItem('localization')).messages) {
    Vue.prototype.$__ = JSON.parse(localStorage.getItem('localization'));
    setTimeout(() => {
            axios.get('/api/localization')
                 .then((response) => {
                    Vue.prototype.$__ = response.data;
                    localStorage.setItem('localization', JSON.stringify(response.data))
                 }, 4000)
        })
} else {
    requestsToMake.push(axios.get('/api/localization'));
}

axios.all(requestsToMake).then(axios.spread(function (response1, response2, response3) {
    Vue.prototype.$user = response1.data.user;
    Vue.prototype.$usersGroups = response1.data.groups;

    if(response2)
    {
        Vue.prototype.$settings = response2.data;
        localStorage.setItem('settings', JSON.stringify(response2.data))
    }

    if(response3)
    {
        localStorage.setItem('localization', JSON.stringify(response3.data))
        Vue.prototype.$__ = response3.data;
    }

    const app = new Vue({
        el: '#otdSpa',
        router: router,
        render: h => h(App),
    });
}));
 
 