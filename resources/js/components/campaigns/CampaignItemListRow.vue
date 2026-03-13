<script setup lang="ts">
import { FileText, ThumbsUp, Mail, Layout, Eye, Copy, Loader2, Coins } from 'lucide-vue-next';
import type { ICampaignItem } from '@/types';

defineProps<{
  items: ICampaignItem[];
  isLoading?: boolean;
}>();

const emit = defineEmits<{
  (e: 'view', item: ICampaignItem): void;
  (e: 'copy', item: ICampaignItem): void;
}>();

const getContentTypeIcon = (type: string) => {
  const t = type.toLowerCase();
  if (t.includes('blog')) return { icon: FileText, color: 'text-purple-600', bg: 'bg-purple-50' };
  if (t.includes('social')) return { icon: ThumbsUp, color: 'text-primary', bg: 'bg-primary/10' };
  if (t.includes('email')) return { icon: Mail, color: 'text-orange-600', bg: 'bg-orange-50' };
  return { icon: Layout, color: 'text-teal-600', bg: 'bg-teal-50' };
};
</script>

<template>
  <div class="w-full">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">Content Items</h2>
      <div v-if="isLoading" class="flex items-center gap-2 text-sm text-slate-400">
        <Loader2 :size="16" class="animate-spin" />
        <span>Syncing...</span>
      </div>
    </div>

    <div
      class="overflow-hidden border border-slate-200 shadow-sm bg-white rounded-2xl dark:border-slate-800 dark:bg-brand-dark"
    >
      <div
        class="hidden md:grid grid-cols-12 px-6 py-4 border-b border-slate-200 bg-slate-50/50 text-[11px] font-bold tracking-wider text-slate-500 uppercase dark:border-slate-800 dark:bg-slate-900/50"
      >
        <div class="col-span-5">Content & Topic</div>
        <div class="col-span-2 text-center">Words</div>
        <div class="col-span-2 text-center">Credits Used</div>
        <div class="col-span-2 text-center">Status</div>
        <div class="col-span-1 text-right">Actions</div>
      </div>

      <div class="divide-y divide-slate-100 dark:divide-slate-800/60">
        <div v-if="items.length === 0 && !isLoading" class="p-12 text-center">
          <div
            class="inline-flex items-center justify-center mb-4 text-slate-400 size-12 rounded-full bg-brand-light dark:bg-slate-800"
          >
            <Layout :size="24" />
          </div>
          <p class="font-medium text-slate-500 dark:text-slate-400">
            No items generated for this campaign yet.
          </p>
        </div>

        <div
          v-for="item in items"
          :key="item.public_id"
          class="transition-all duration-200 group hover:bg-primary/5 dark:hover:bg-primary/5"
        >
          <div class="p-5 space-y-4 md:hidden">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div :class="[getContentTypeIcon(item.content_type).bg, 'p-2 rounded-xl']">
                  <component
                    :is="getContentTypeIcon(item.content_type).icon"
                    :size="20"
                    :class="getContentTypeIcon(item.content_type).color"
                  />
                </div>
                <div>
                  <h4 class="text-sm font-bold text-slate-900 dark:text-white">
                    {{ item.content_type }}
                  </h4>
                  <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-[10px] text-slate-400 font-mono"
                      >{{ item.word_count }} words</span
                    >
                    <span class="text-[10px] text-amber-500 font-bold flex items-center gap-0.5">
                      <Coins :size="10" /> {{ item.credit_used ?? 0 }}
                    </span>
                  </div>
                </div>
              </div>
              <span
                :class="[
                  'text-[10px] font-bold uppercase px-2 py-1 rounded',
                  item.status === 'completed'
                    ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400'
                    : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400',
                ]"
              >
                {{ item.status }}
              </span>
            </div>

            <p
              class="text-sm leading-relaxed text-slate-600 line-clamp-2 dark:text-slate-400 italic"
            >
              "{{ item.topic }}"
            </p>

            <div class="flex gap-2 pt-2">
              <button
                @click="emit('view', item)"
                class="flex items-center justify-center flex-1 gap-2 py-3 text-sm font-bold transition-all cursor-pointer rounded-xl bg-brand-light text-slate-700 active:scale-95 dark:bg-slate-800 dark:text-slate-200"
              >
                <Eye :size="16" /> View
              </button>
              <button
                @click="emit('copy', item)"
                class="flex items-center justify-center px-4 py-3 transition-all cursor-pointer rounded-xl bg-brand-light text-slate-700 active:scale-95 dark:bg-slate-800 dark:text-slate-200"
              >
                <Copy :size="16" />
              </button>
            </div>
          </div>

          <div class="hidden w-full px-6 py-4 md:grid grid-cols-12 items-center">
            <div class="flex items-start col-span-5 gap-3">
              <div
                :class="[
                  getContentTypeIcon(item.content_type).bg,
                  'w-10 h-10 mt-1 rounded-xl flex items-center justify-center transition-transform group-hover:scale-105 shrink-0',
                ]"
              >
                <component
                  :is="getContentTypeIcon(item.content_type).icon"
                  :size="20"
                  :class="getContentTypeIcon(item.content_type).color"
                />
              </div>
              <div class="min-w-0 pr-4">
                <span class="text-sm font-bold text-slate-900 dark:text-slate-100 block">{{
                  item.content_type
                }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400 truncate block mt-0.5">{{
                  item.topic
                }}</span>
              </div>
            </div>

            <div class="col-span-2 font-mono text-xs text-center text-slate-500">
              {{ item.word_count.toLocaleString() }}
            </div>

            <div class="col-span-2 flex flex-col items-center justify-center">
              <div
                class="flex items-center gap-1.5 text-sm font-bold text-slate-700 dark:text-slate-300"
              >
                <Coins :size="14" class="text-amber-500" />
                {{ item.credit_used ?? 0 }}
              </div>
            </div>

            <div class="flex justify-center col-span-2">
              <span
                :class="[
                  'text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full',
                  item.status === 'completed'
                    ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400'
                    : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400',
                ]"
              >
                {{ item.status }}
              </span>
            </div>

            <div class="flex justify-end col-span-1 gap-1">
              <button
                @click="emit('view', item)"
                class="p-2 transition-all cursor-pointer rounded-xl text-slate-400 hover:bg-primary/10 hover:text-primary"
              >
                <Eye :size="18" />
              </button>
              <button
                @click="emit('copy', item)"
                class="p-2 transition-all cursor-pointer rounded-xl text-slate-400 hover:bg-primary/10 hover:text-primary"
              >
                <Copy :size="18" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
