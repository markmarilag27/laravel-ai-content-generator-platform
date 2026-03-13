<script setup lang="ts">
import { Calendar, Trash2, Rocket, Eye } from 'lucide-vue-next';
import type { ICampaign } from '@/types';

defineProps<{
  item: ICampaign;
}>();

const emit = defineEmits<{
  (e: 'delete', id: string): void;
  (e: 'view', id: string): void;
}>();

const formatDate = (date: string | null) => {
  if (!date) return 'No deadline';
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  });
};
</script>

<template>
  <div
    class="group w-full border-b border-slate-100 dark:border-slate-800/60 last:border-0 hover:bg-blue-50/30 dark:hover:bg-blue-900/10 transition-all duration-200"
  >
    <div class="md:hidden p-4">
      <div class="flex flex-col gap-4">
        <div class="flex items-center gap-3">
          <div
            class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary"
          >
            <Rocket :size="20" />
          </div>
          <div class="min-w-0">
            <h4 class="font-bold text-slate-900 dark:text-white truncate text-base">
              {{ item.title }}
            </h4>
            <div class="flex items-center gap-2 mt-1">
              <span
                :class="[
                  'text-[10px] font-bold uppercase px-1.5 py-0.5 rounded',
                  item.status_class,
                ]"
              >
                {{ item.status }}
              </span>
              <span class="text-[11px] text-slate-500 flex items-center gap-1">
                <Calendar :size="10" /> {{ formatDate(item.deadline) }}
              </span>
            </div>
          </div>
        </div>

        <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
          <div
            class="bg-primary h-full transition-all duration-500"
            :style="{ width: item.progress_percentage + '%' }"
          ></div>
        </div>

        <div class="flex gap-2">
          <button
            @click="emit('view', item.public_id)"
            class="flex-1 cursor-pointer flex items-center justify-center gap-2 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-sm font-semibold"
          >
            <Eye :size="16" /> View Details
          </button>
          <button
            :disabled="!!item.campaign_items_count"
            @click="emit('delete', item.public_id)"
            class="flex cursor-pointer items-center justify-center w-12 py-2.5 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400"
          >
            <Trash2 :size="16" />
          </button>
        </div>
      </div>
    </div>

    <div class="hidden md:grid grid-cols-12 items-center px-6 py-4 w-full">
      <div class="col-span-5 flex items-center gap-3">
        <div
          class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center text-primary shadow-sm"
        >
          <Rocket :size="18" />
        </div>
        <div class="min-w-0">
          <div
            class="font-semibold text-slate-900 dark:text-slate-100 truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors"
          >
            {{ item.title }}
          </div>
          <div class="text-[11px] text-slate-500 mt-0.5">
            {{ item.campaign_items_count }} content pieces
          </div>
        </div>
      </div>

      <div class="col-span-2 flex items-center gap-2 text-sm text-slate-500">
        <Calendar :size="14" class="opacity-50" />
        <span>{{ formatDate(item.deadline) }}</span>
      </div>

      <div class="col-span-2 flex justify-center">
        <span
          :class="[
            'text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full',
            item.status_class,
          ]"
        >
          {{ item.status }}
        </span>
      </div>

      <div class="col-span-2 px-2">
        <div
          class="flex justify-between items-center mb-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-tight"
        >
          <span>Progress</span>
          <span>{{ item.progress_percentage }}%</span>
        </div>
        <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
          <div
            class="bg-primary h-full transition-all duration-500"
            :style="{ width: item.progress_percentage + '%' }"
          ></div>
        </div>
      </div>

      <div class="col-span-1 flex justify-end items-center gap-1">
        <button
          @click="emit('view', item.public_id)"
          class="p-2 cursor-pointer rounded-xl hover:bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200"
        >
          <Eye :size="16" />
        </button>
        <button
          :disabled="!!item.campaign_items_count"
          @click="emit('delete', item.public_id)"
          class="p-2 cursor-pointer text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-all"
        >
          <Trash2 :size="18" />
        </button>
      </div>
    </div>
  </div>
</template>
