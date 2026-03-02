import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { VueQueryPlugin } from '@tanstack/vue-query';
import { queryClient } from './lib/query-client.lib';
import router from './router';
import App from './App.vue';

const element = document.getElementById('app');

if (element) {
  const app = createApp(App);
  const pinia = createPinia();

  app.use(VueQueryPlugin, { queryClient });
  app.use(pinia);
  app.use(router);

  router.isReady().then(() => {
    app.mount(element);
  });
}
