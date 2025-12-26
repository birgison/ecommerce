import { createApp } from 'vue';
import ExampleComponent from './components/ExampleComponent.vue';

createApp({})
    .component('example-component', ExampleComponent)
    .mount('#app');

