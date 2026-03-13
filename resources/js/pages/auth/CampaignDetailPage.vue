<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useQueryClient } from '@tanstack/vue-query';
import { Calendar, RefreshCw, X, Copy, Check, FileText } from 'lucide-vue-next';
import { useCampaigns } from '@/composables/useCampaigns';
import { useWebsocketStore } from '@/stores/websocket.store';
import { QUERY_KEYS } from '@/lib/constant.lib';
import type { IBroadcastCampaign, ICampaignItem } from '@/types';
import CampaignItemListRow from '@/components/campaigns/CampaignItemListRow.vue';
import { useClipboard } from '@/composables/useClipboard';

const route = useRoute();
const queryClient = useQueryClient();
const wsStore = useWebsocketStore();
const { isCopied, copy } = useClipboard();
const { useDetailQuery, useListItemsQuery } = useCampaigns();

const publicId = ref(route.params.id as string);
const page = ref(1);
const progress = ref(0);

const { data, isLoading } = useDetailQuery(publicId);
const {
  data: items,
  isLoading: isListingItemsLoading,
  refetch,
} = useListItemsQuery(publicId, page);

const campaign = computed(() => data.value?.data.data);

const isModalOpen = ref(false);
const selectedItem = ref<ICampaignItem | null>(null);

const handleView = (item: ICampaignItem) => {
  selectedItem.value = item;
  isModalOpen.value = true;
};

const handleCopy = async (item: ICampaignItem) => {
  const content = item.content;
  if (!content) return;

  try {
    await copy(content);
    isCopied.value = true;
    setTimeout(() => (isCopied.value = false), 2000);
  } catch (err) {
    console.error('Failed to copy text: ', err);
  }
};

const closeModal = () => {
  isModalOpen.value = false;
  selectedItem.value = null;
};

onMounted(() => {
  wsStore.connect();

  if (wsStore.echo) {
    wsStore.echo
      .private(`campaign.${publicId.value}`)
      .listen('CampaignStatusUpdated', (e: IBroadcastCampaign) => {
        console.log('WS Event Received:', e);
        progress.value = e.percentage_complete;

        if (e.percentage_complete === 100) {
          queryClient.invalidateQueries({ queryKey: [QUERY_KEYS.campaign] });
          refetch();
        }
      });
  }
});

onUnmounted(() => {
  if (wsStore.echo) {
    wsStore.echo.leave(`campaign.${publicId.value}`);
  }
});

watch(
  () => campaign.value,
  (data) => {
    if (data?.progress_percentage) {
      progress.value = data.progress_percentage;
    }
  }
);

const formatDate = (date: string | null) => {
  if (!date) return 'No deadline set';
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  });
};
</script>

<template>
  <main v-if="campaign" class="flex-1 px-6 py-8 max-w-[1200px] mx-auto w-full">
    <div
      class="bg-white dark:bg-brand-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-8 mb-8"
    >
      <div class="flex flex-wrap justify-between items-start gap-4">
        <div class="flex flex-col gap-2">
          <div class="flex items-center gap-3">
            <h1 class="text-slate-900 dark:text-slate-100 text-3xl font-bold leading-tight">
              {{ campaign.title }}
            </h1>
            <span
              :class="campaign.status_class"
              class="px-3 py-1 rounded-full text-xs font-semibold tracking-wide uppercase"
            >
              {{ campaign.status }}
            </span>
          </div>
          <p class="text-slate-500 text-sm font-medium flex items-center gap-2">
            <Calendar :size="16" />
            Deadline: {{ formatDate(campaign.deadline) }}
          </p>
        </div>
      </div>

      <div
        v-if="progress < 100"
        class="bg-slate-50 dark:bg-brand-dark/50 rounded-lg p-5 border border-slate-100 dark:border-slate-800 mt-6"
      >
        <div class="flex justify-between items-end mb-2">
          <div>
            <h3 class="text-slate-700 dark:text-slate-300 text-sm font-semibold mb-1">
              Campaign Progress
            </h3>
            <p class="text-slate-500 text-xs">Processing items in background...</p>
          </div>
          <span class="text-primary text-xl font-bold">{{ progress }}%</span>
        </div>
        <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-2.5 overflow-hidden">
          <div
            class="bg-primary h-2.5 rounded-full transition-all duration-700 ease-out"
            :style="{ width: `${progress}%` }"
          ></div>
        </div>
      </div>
    </div>

    <CampaignItemListRow
      :items="items?.data.data ?? []"
      :is-loading="isListingItemsLoading"
      @view="handleView"
      @copy="handleCopy"
    />

    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isModalOpen && selectedItem"
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6"
      >
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModal"></div>

        <div
          class="relative w-full max-w-3xl overflow-hidden bg-white dark:bg-brand-dark rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col max-h-[90vh]"
        >
          <div
            class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800"
          >
            <div class="flex items-center gap-3">
              <div class="p-2 bg-primary/10 rounded-lg text-primary">
                <FileText :size="20" />
              </div>
              <div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100 leading-tight">
                  {{ selectedItem.content_type }}
                </h3>
                <p class="text-xs text-slate-500 truncate max-w-[200px] sm:max-w-md">
                  {{ selectedItem.topic }}
                </p>
              </div>
            </div>
            <button
              @click="closeModal"
              class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors rounded-full hover:bg-slate-100 dark:hover:bg-slate-800"
            >
              <X :size="20" />
            </button>
          </div>

          <div class="flex-1 overflow-y-auto p-6 bg-slate-50/50 dark:bg-slate-900/30">
            <div
              v-if="selectedItem.status === 'completed'"
              class="prose prose-slate dark:prose-invert max-w-none"
            >
              <div
                class="whitespace-pre-wrap text-slate-700 dark:text-slate-300 text-sm leading-relaxed"
              >
                {{ selectedItem.content }}
              </div>
            </div>
            <div v-else class="flex flex-col items-center justify-center py-20 text-slate-400">
              <p class="text-sm font-medium">Content is still being generated...</p>
            </div>
          </div>

          <div
            class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between bg-white dark:bg-brand-dark"
          >
            <div class="text-[11px] font-mono text-slate-400 uppercase tracking-wider">
              ID: {{ selectedItem.public_id.split('-')[0] }}
            </div>
            <div class="flex items-center gap-2">
              <button
                @click="handleCopy(selectedItem)"
                class="flex cursor-pointer items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition-all active:scale-95 disabled:opacity-50"
                :disabled="selectedItem.status !== 'completed'"
              >
                <Check v-if="isCopied" :size="16" />
                <Copy v-else :size="16" />
                {{ isCopied ? 'Copied!' : 'Copy Content' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </main>

  <div v-else-if="isLoading" class="flex flex-col items-center justify-center min-h-[60vh]">
    <RefreshCw class="animate-spin text-primary size-10 mb-4" />
    <p class="text-slate-500 font-medium">Loading Campaign Details...</p>
  </div>
</template>

<style scoped>
/* Ensure the modal content is readable */
.prose {
  line-height: 1.6;
}
</style>
