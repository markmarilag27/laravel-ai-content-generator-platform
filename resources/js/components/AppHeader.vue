<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import { Sparkles, Coins, LogOut, Sun, Moon, Menu, X } from 'lucide-vue-next';
import { useAuthStore } from '@/stores/auth.store';

const route = useRoute();
const authStore = useAuthStore();
const isDark = ref(false);
const isMobileMenuOpen = ref(false);
const headerRef = ref<HTMLElement | null>(null);

const links = ref([
  { text: 'Brand Voices', to: '/', base: '/brand-voice' },
  { text: 'Contents', to: '/contents', base: '/contents' },
  { text: 'Campaigns', to: '/campaigns', base: '/campaigns' },
]);

const isLinkActive = (link: { to: string; base: string }) => {
  if (link.to === '/' && route.path === '/') return true;
  if (link.to === '/') return route.path.startsWith('/brand-voice');
  return route.path.startsWith(link.base);
};

const toggleTheme = () => {
  isDark.value = !isDark.value;
  document.documentElement.classList.toggle('dark', isDark.value);
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
};

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const handleLogout = async () => {
  await authStore.logout();
  window.location.href = '/login';
};

const handleClickOutside = (event: MouseEvent) => {
  const target = event.target as Node;
  if (isMobileMenuOpen.value && headerRef.value && !headerRef.value.contains(target)) {
    isMobileMenuOpen.value = false;
  }
};

onMounted(() => {
  isDark.value =
    localStorage.getItem('theme') === 'dark' ||
    (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
  document.documentElement.classList.toggle('dark', isDark.value);
  window.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  window.removeEventListener('click', handleClickOutside);
});
</script>

<template>
  <header
    ref="headerRef"
    class="sticky top-0 z-50 lg:flex w-full lg:justify-items-center items-center border-b border-slate-200 bg-white px-4 py-3 transition-colors duration-300 dark:border-slate-800 dark:bg-app-bg md:px-10"
  >
    <div class="w-full mx-auto flex max-w-7xl items-center justify-between">
      <RouterLink
        to="/"
        class="flex items-center gap-2 text-slate-900 dark:text-slate-100 hover:opacity-90 transition-opacity md:gap-4"
      >
        <div class="flex size-6 items-center justify-center text-primary">
          <Sparkles :size="24" fill="currentColor" class="fill-primary/20" />
        </div>
        <h2 class="text-base font-bold leading-tight tracking-tight md:text-lg">AI Content Gen</h2>
      </RouterLink>

      <nav class="hidden lg:flex items-center gap-8">
        <RouterLink
          v-for="(link, index) in links"
          :key="index"
          :to="link.to"
          class="text-sm font-medium transition-colors hover:text-primary dark:hover:text-primary"
          :class="[isLinkActive(link) ? 'text-primary' : 'text-slate-600 dark:text-slate-400']"
        >
          {{ link.text }}
        </RouterLink>
      </nav>

      <div class="hidden lg:flex items-center gap-4">
        <div
          class="flex items-center gap-2 rounded-full bg-primary/10 px-3 py-1.5 text-sm font-medium text-primary dark:bg-primary/20"
        >
          <Coins :size="16" />
          <span>{{ authStore.creditsRemaining }} Credits</span>
        </div>

        <button
          @click="toggleTheme"
          class="flex cursor-pointer size-10 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition-colors hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-slate-100"
        >
          <Sun v-if="isDark" :size="18" />
          <Moon v-else :size="18" />
        </button>

        <button
          @click="handleLogout"
          class="flex cursor-pointer size-10 items-center justify-center rounded-lg bg-slate-100 text-red-500 transition-colors hover:bg-red-50 hover:text-red-600 dark:bg-slate-800 dark:text-red-400 dark:hover:bg-red-500/20"
          title="Logout"
        >
          <LogOut :size="18" />
        </button>
      </div>

      <button
        @click.stop="toggleMobileMenu"
        class="flex cursor-pointer size-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600 transition-colors hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 dark:hover:text-slate-100 lg:hidden"
      >
        <X v-if="isMobileMenuOpen" :size="20" />
        <Menu v-else :size="20" />
      </button>
    </div>

    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="transform -translate-y-4 opacity-0"
      enter-to-class="transform translate-y-0 opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="transform translate-y-0 opacity-100"
      leave-to-class="transform -translate-y-4 opacity-0"
    >
      <nav
        v-if="isMobileMenuOpen"
        @click.stop
        class="absolute left-0 top-full flex w-full flex-col border-b border-slate-200 bg-white p-4 shadow-xl dark:border-slate-800 dark:bg-app-bg lg:hidden"
      >
        <ul class="flex flex-col gap-1">
          <li v-for="(link, index) in links" :key="index">
            <RouterLink
              :to="link.to"
              @click="isMobileMenuOpen = false"
              class="block rounded-lg px-3 py-2.5 text-base font-medium transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/50 force-h-auto"
              :class="[
                isLinkActive(link)
                  ? 'text-primary bg-primary/5 dark:bg-primary/10'
                  : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50',
              ]"
            >
              {{ link.text }}
            </RouterLink>
          </li>
        </ul>

        <div class="mt-4 flex flex-col gap-2 border-t border-slate-100 pt-4 dark:border-slate-800">
          <div
            class="flex items-center justify-between rounded-lg bg-primary/5 px-3 py-3 dark:bg-primary/10"
          >
            <div class="flex items-center gap-2 text-sm font-semibold text-primary">
              <Coins :size="18" />
              <span>Credits Available</span>
            </div>
            <span class="text-sm font-bold text-primary">{{ authStore.creditsRemaining }}</span>
          </div>

          <button
            @click="toggleTheme"
            class="flex cursor-pointer items-center justify-between rounded-lg px-3 py-3 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-800/50"
          >
            <div class="flex items-center gap-2">
              <Sun v-if="isDark" :size="18" />
              <Moon v-else :size="18" />
              <span>{{ isDark ? 'Light Mode' : 'Dark Mode' }}</span>
            </div>
          </button>

          <button
            @click="handleLogout"
            class="flex cursor-pointer items-center gap-2 rounded-lg px-3 py-3 text-sm font-medium text-red-500 transition-colors hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10"
          >
            <LogOut :size="18" />
            <span>Logout</span>
          </button>
        </div>
      </nav>
    </Transition>
  </header>
</template>

<style scoped>
.force-h-auto {
  height: auto;
}
</style>
