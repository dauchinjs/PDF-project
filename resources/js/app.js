import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import Home from "./Components/Home.vue";

const app = createApp({});
app.component('Home', Home);
app.mount('#home');
