<script setup lang="ts">
import { onMounted, watch } from 'vue';
import { RouterView } from 'vue-router';
import { useAuthStore } from '@/stores/auth.store';
import { useWebsocketStore } from '@/stores/websocket.store';

const authStore = useAuthStore();
const wsStore = useWebsocketStore();

// 1. Handle initial session check on hard refresh
onMounted(async () => {
  await authStore.fetchUser();
});

// 2. Reactively manage the WebSocket connection
watch(
  () => authStore.isAuthenticated,
  (isAuth) => {
    if (isAuth) {
      wsStore.connect();
    } else {
      wsStore.disconnect();
    }
  },
  { immediate: true } // Handles the case where the user is already authed on load
);
</script>

<template>
  <RouterView v-if="authStore.initialized" />

  <div v-else class="h-screen w-screen flex items-center justify-center bg-slate-50">
    <div class="text-slate-400 animate-pulse">Initializing Application...</div>
  </div>
</template>
