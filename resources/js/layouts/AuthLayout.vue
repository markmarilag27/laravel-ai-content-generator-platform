<script setup lang="ts">
import { useAuthStore } from '@/stores/auth.store';

const authStore = useAuthStore();

const handleLogout = async () => {
  await authStore.logout();
  window.location.href = '/login';
};
</script>

<template>
  <div class="min-h-screen bg-slate-50 flex flex-col">
    <header class="sticky top-0 z-50 w-full bg-white border-b border-slate-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex-shrink-0 flex items-center">
            <RouterLink to="/" class="text-xl font-bold tracking-tight text-indigo-600">
              AI Platform
            </RouterLink>
          </div>

          <nav class="hidden md:flex space-x-8">
            <RouterLink
              to="/"
              class="inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2 transition-colors duration-200"
              active-class="border-indigo-500 text-slate-900"
              exact-active-class="border-indigo-500 text-slate-900"
              :class="[
                $route.name !== 'Dashboard'
                  ? 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'
                  : '',
              ]"
            >
              Dashboard
            </RouterLink>
            <RouterLink
              to="/campaigns"
              class="inline-flex items-center px-1 pt-1 text-sm font-medium border-b-2 transition-colors duration-200"
              active-class="border-indigo-500 text-slate-900"
              :class="[
                !$route.path.startsWith('/campaigns')
                  ? 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'
                  : '',
              ]"
            >
              Campaigns
            </RouterLink>
          </nav>

          <div class="flex items-center space-x-4">
            <div class="hidden sm:block text-right">
              <p class="text-xs font-semibold text-slate-900 leading-none">
                {{ authStore.user?.name }}
              </p>
              <p class="text-[10px] text-slate-500 mt-1">
                {{ authStore.user?.email }}
              </p>
            </div>
            <button
              @click="handleLogout"
              class="ml-4 cursor-pointer text-sm font-semibold text-slate-600 hover:text-red-600 transition-colors"
            >
              Sign out
            </button>
          </div>
        </div>
      </div>
    </header>

    <main class="flex-1">
      <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <RouterView />
      </div>
    </main>
  </div>
</template>

<style scoped>
/* Ensure smooth transitions for the active border */
nav a {
  height: 64px;
}
</style>
