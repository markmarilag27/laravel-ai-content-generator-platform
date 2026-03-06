<script setup lang="ts">
import { Plus } from 'lucide-vue-next';

defineEmits<{ (e: 'create'): void }>();
</script>

<template>
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Brand Voices</h1>
      <p class="text-sm text-slate-500 dark:text-slate-400">Manage your AI personality profiles</p>
    </div>
    <button
      @click="$emit('create')"
      class="inline-flex items-center cursor-pointer justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-all shadow-sm active:scale-95 w-full sm:w-auto"
    >
      <Plus :size="18" :stroke-width="2.5" />
      <span>New Profile</span>
    </button>
  </div>
</template>
