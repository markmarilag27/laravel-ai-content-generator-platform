import { defineStore } from 'pinia';
import { shallowRef } from 'vue';
import type Echo from 'laravel-echo';
import { createEchoInstance } from '@/services/echo';

export const useWebsocketStore = defineStore('websocket', () => {
  const echo = shallowRef<Echo<'reverb'> | null>(null);

  function connect() {
    if (echo.value) return;
    echo.value = createEchoInstance();
  }

  function disconnect() {
    if (echo.value) {
      echo.value.disconnect();
      echo.value = null;
    }
  }

  return { echo, connect, disconnect };
});
