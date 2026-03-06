<script setup lang="ts">
import { Sparkles } from 'lucide-vue-next';
const currentYear = new Date().getFullYear();
</script>

<template>
  <footer
    class="flex border-t border-slate-200 bg-white px-6 py-8 transition-colors duration-300 dark:border-slate-800 dark:bg-app-bg md:px-10"
  >
    <div
      class="mx-auto flex max-w-7xl flex-col items-center gap-8 md:flex-row md:justify-between md:gap-6"
    >
      <div class="flex flex-col items-center gap-3 md:items-start md:gap-2">
        <div class="flex items-center gap-2 text-slate-900 dark:text-slate-100">
          <Sparkles :size="20" class="text-primary fill-primary/10" />
          <span class="font-bold tracking-tight">AI Content Gen</span>
        </div>
        <p class="text-xs text-slate-500 dark:text-slate-400">
          © {{ currentYear }} AI Content Gen. All rights reserved.
        </p>
      </div>
    </div>
  </footer>
</template>
