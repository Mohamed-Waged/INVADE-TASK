require('./bootstrap');
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

require('alpinejs');

import { createApp } from 'vue';
import TaskList from './components/TaskList.vue';

const app = createApp(TaskList);
app.mount('#app');
