<script setup lang="ts">
import type { IBrandVoiceProfile } from '@/types';
import { Layers } from 'lucide-vue-next';
import BrandVoiceRow from './BrandVoiceRow.vue';

defineProps<{
  profiles: IBrandVoiceProfile[];
}>();

const emit = defineEmits<{ (e: 'edit', id: string): void; (e: 'delete', id: string): void }>();
</script>

<template>
  <div
    class="w-full bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800/60 shadow-sm overflow-hidden"
  >
    <div
      class="hidden sm:grid grid-cols-12 px-6 py-4 bg-slate-50/50 dark:bg-slate-800/40 border-b border-slate-200 dark:border-slate-800"
    >
      <div
        class="col-span-5 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest"
      >
        Brand Identity
      </div>
      <div
        class="col-span-4 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest"
      >
        Last Activity
      </div>
      <div
        class="col-span-3 text-right text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest"
      >
        Actions
      </div>
    </div>

    <div class="w-full divide-y divide-slate-100 dark:divide-slate-800/60">
      <BrandVoiceRow
        v-for="profile in profiles"
        :key="profile.public_id"
        :item="profile"
        @edit="emit('edit', profile.public_id)"
        @delete="emit('delete', profile.public_id)"
      />

      <div
        v-if="profiles.length === 0"
        class="w-full flex flex-col items-center justify-center p-20 text-center"
      >
        <div
          class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4"
        >
          <Layers class="text-slate-300 dark:text-slate-600" :size="32" />
        </div>
        <h3 class="text-slate-900 dark:text-white font-medium">No voices found</h3>
        <p class="text-sm text-slate-500 dark:text-slate-500 max-w-[240px] mt-1">
          Create your first brand voice profile to keep your AI content consistent.
        </p>
      </div>
    </div>
  </div>
</template>
