<script setup lang="ts">
import { Pencil, Clock, Hash, Trash2 } from 'lucide-vue-next';
import type { IBrandVoiceProfile } from '@/types';

defineProps<{
  item: IBrandVoiceProfile;
}>();

const emit = defineEmits<{
  (e: 'edit', id: string): void;
  (e: 'delete', id: string): void;
}>();

const formatDate = (date: string) =>
  new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
  });
</script>

<template>
  <div
    class="group w-full border-b border-slate-100 dark:border-slate-800/60 last:border-0 hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-all duration-200"
  >
    <div class="sm:hidden p-4">
      <div class="flex flex-col gap-4">
        <div class="flex justify-between items-start">
          <div class="min-w-0">
            <h4 class="font-bold text-slate-900 dark:text-white truncate text-base">
              {{ item.name }}
            </h4>
            <div class="flex items-center gap-3 mt-1.5">
              <span
                class="flex items-center gap-1 text-[11px] font-mono text-slate-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded"
              >
                <Hash :size="10" /> {{ item.public_id.split('_')[1] }}
              </span>
              <span class="flex items-center gap-1 text-[11px] text-slate-500">
                <Clock :size="10" /> {{ formatDate(item.updated_at) }}
              </span>
            </div>
          </div>
        </div>

        <div class="flex gap-2">
          <button
            @click="emit('edit', item.public_id)"
            class="flex-1 cursor-pointer flex items-center justify-center gap-2 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm font-semibold active:scale-95 transition-transform"
          >
            <Pencil :size="16" />
            Edit
          </button>
          <button
            @click="emit('delete', item.public_id)"
            class="flex cursor-pointer items-center justify-center w-12 py-2.5 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 active:scale-95 transition-transform"
          >
            <Trash2 :size="16" />
          </button>
        </div>
      </div>
    </div>

    <div class="hidden sm:grid grid-cols-12 items-center px-6 py-4 w-full">
      <div class="col-span-5 flex items-center gap-3">
        <div
          class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xs shadow-sm"
        >
          {{ item.name.charAt(0).toUpperCase() }}
        </div>
        <div class="min-w-0">
          <div
            class="font-semibold text-slate-900 dark:text-slate-100 truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors"
          >
            {{ item.name }}
          </div>
          <div class="text-[11px] font-mono text-slate-400">ID: {{ item.public_id }}</div>
        </div>
      </div>

      <div class="col-span-4 flex items-center gap-2 text-sm text-slate-500">
        <Clock :size="14" class="opacity-50" />
        <span>{{ formatDate(item.updated_at) }}</span>
      </div>

      <div class="col-span-3 flex justify-end items-center gap-1">
        <button
          @click="emit('edit', item.public_id)"
          class="p-2 cursor-pointer text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-all"
          title="Edit Profile"
        >
          <Pencil :size="18" />
        </button>
        <button
          @click="emit('delete', item.public_id)"
          class="p-2 cursor-pointer text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-all"
          title="Delete Profile"
        >
          <Trash2 :size="18" />
        </button>
      </div>
    </div>
  </div>
</template>
