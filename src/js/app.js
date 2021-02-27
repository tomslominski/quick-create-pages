import { createApp } from 'vue';
import QcpAppComponent from './components/qcp-app';
import PageItemComponent from './components/page-item';
import PageListComponent from './components/page-list';
import Translate from './translate';

createApp({})
.component('qcp-app', QcpAppComponent)
.component('page-item', PageItemComponent)
.component('page-list', PageListComponent)
.use(Translate)
.mount("#qcp-app");
