<script setup lang="ts">
import { Loader2 } from 'lucide-vue-next';

defineProps({
  isLoading: Boolean,
});
</script>

<template>
  <div v-if="isLoading" class="flex flex-col items-center justify-center py-20 gap-3">
    <Loader2 class="animate-spin text-blue-600" :size="32" />
    <p class="text-sm text-slate-500 animate-pulse">Fetching...</p>
  </div>
</template>
