<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { Plus, Newspaper, Hash, Coins, Calendar, X, Copy, Check } from 'lucide-vue-next';
import { useBrandVoices } from '@/composables/useBrandVoices';
import AppLoader from '@/components/AppLoader.vue';
import { IContent } from '@/types';
import { useClipboard } from '@/composables/useClipboard';

const router = useRouter();
const currentPage = ref(1);

const { useListContentQuery } = useBrandVoices();
const { isCopied, copy } = useClipboard();
const { data, isLoading } = useListContentQuery(currentPage);

const contents = computed<IContent[]>(() => data.value?.data.data ?? []);

const selectedContent = ref<IContent | null>(null);

const openModal = (item: IContent): void => {
  selectedContent.value = item;
};

const closeModal = (): void => {
  selectedContent.value = null;
  isCopied.value = false;
};

const copyToClipboard = async (): Promise<void> => {
  if (!selectedContent.value) return;
  try {
    await copy(selectedContent.value.body);
    isCopied.value = true;
    setTimeout(() => (isCopied.value = false), 2000);
  } catch (err) {
    console.error('Failed to copy text: ', err);
  }
};

const formatDate = (date: string): string =>
  new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  });

const handleGenerate = async (): Promise<void> => {
  await router.push({ name: 'ContentsCreate' });
};

const handleKeyDown = (e: KeyboardEvent) => {
  if (e.key === 'Escape' && selectedContent.value) closeModal();
};

onMounted(() => window.addEventListener('keydown', handleKeyDown));
onUnmounted(() => window.removeEventListener('keydown', handleKeyDown));
</script>

<template>
  <div class="flex flex-col gap-6 md:gap-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h1
          class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-100"
        >
          Generated Content
        </h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
          Manage and review all AI-generated copy across your brand profiles.
        </p>
      </div>

      <button
        @click="handleGenerate"
        class="inline-flex cursor-pointer items-center justify-center gap-2 px-5 py-2.5 bg-primary text-white rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all active:scale-95 whitespace-nowrap"
      >
        <Plus :size="18" />
        Generate Content
      </button>
    </div>

    <div v-if="isLoading" class="py-20">
      <AppLoader message="Fetching your content library..." />
    </div>

    <div
      v-else-if="contents.length === 0"
      class="flex flex-col items-center justify-center py-20 px-6 bg-white dark:bg-slate-900 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-3xl text-center"
    >
      <div
        class="size-16 bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center text-slate-400 mb-4"
      >
        <Newspaper :size="32" />
      </div>
      <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">No content found</h3>
      <p class="mt-1 text-sm text-slate-500 dark:text-slate-400 max-w-xs mx-auto">
        Your generated content library is currently empty. Start by creating a new piece of content.
      </p>
    </div>

    <div v-else class="grid gap-4">
      <div
        v-for="item in contents"
        :key="item.public_id"
        class="group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden hover:border-primary/50 hover:shadow-md transition-all"
      >
        <div class="p-5 md:p-6">
          <div class="flex flex-col md:flex-row gap-4 md:gap-6">
            <div class="flex-1 space-y-3">
              <div
                class="flex items-center gap-3 text-[10px] font-bold uppercase tracking-widest text-slate-400"
              >
                <span
                  class="flex items-center gap-1 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded"
                >
                  <Hash :size="10" /> {{ item.public_id.split('-')[0] }}
                </span>
                <span class="flex items-center gap-1">
                  <Calendar :size="10" /> {{ formatDate(item.created_at) }}
                </span>
              </div>
              <p
                class="text-slate-700 dark:text-slate-300 leading-relaxed line-clamp-3 md:line-clamp-2 italic text-sm md:text-base"
              >
                "{{ item.body }}"
              </p>
            </div>

            <div
              class="flex flex-row md:flex-col justify-between md:justify-center md:items-end border-t md:border-t-0 md:border-l border-slate-100 dark:border-slate-800 pt-4 md:pt-0 md:pl-6 gap-4"
            >
              <div class="flex flex-col md:items-end">
                <span class="text-[10px] text-slate-400 uppercase font-bold tracking-tight"
                  >Cost</span
                >
                <span
                  class="flex items-center gap-1.5 text-sm font-semibold text-slate-900 dark:text-slate-100"
                >
                  <Coins :size="14" class="text-amber-500" />
                  {{ item.tokens_used }}
                </span>
              </div>

              <button
                @click="openModal(item)"
                class="text-sm cursor-pointer font-bold text-primary hover:underline flex items-center gap-1 whitespace-nowrap"
              >
                View Full Content
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <Teleport to="body">
      <Transition name="modal-fade">
        <div
          v-if="selectedContent"
          class="fixed inset-0 z-[9999] flex items-end sm:items-center justify-center bg-slate-900/60 backdrop-blur-sm p-0 sm:p-4"
          @click="closeModal"
        >
          <div
            class="modal-container bg-white dark:bg-slate-900 w-full max-w-2xl rounded-t-3xl sm:rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col max-h-[95vh] sm:max-h-[90vh]"
            @click.stop
          >
            <div
              class="p-5 md:p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between"
            >
              <div class="flex items-center gap-3">
                <div
                  class="size-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0"
                >
                  <Newspaper :size="20" />
                </div>
                <div class="min-w-0">
                  <h3 class="font-bold text-slate-900 dark:text-slate-100 truncate">
                    Content Preview
                  </h3>
                  <p class="text-[10px] md:text-xs text-slate-500 truncate">
                    ID: {{ selectedContent.public_id }}
                  </p>
                </div>
              </div>
              <button
                @click="closeModal"
                class="size-9 rounded-full flex items-center justify-center bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors cursor-pointer"
              >
                <X :size="20" />
              </button>
            </div>

            <div class="p-6 md:p-8 overflow-y-auto custom-scrollbar">
              <div class="prose dark:prose-invert max-w-none">
                <p
                  class="text-slate-700 dark:text-slate-300 whitespace-pre-wrap leading-relaxed text-base md:text-lg italic"
                >
                  {{ selectedContent.body }}
                </p>
              </div>
            </div>

            <div
              class="p-5 md:p-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20 flex flex-col sm:flex-row items-center justify-between gap-4 rounded-b-3xl"
            >
              <div
                class="flex items-center gap-6 text-[10px] md:text-xs text-slate-500 font-medium"
              >
                <span class="flex items-center gap-1.5"
                  ><Coins :size="14" class="text-amber-500" />
                  {{ selectedContent.tokens_used }}</span
                >
                <span class="flex items-center gap-1.5"
                  ><Calendar :size="14" /> {{ formatDate(selectedContent.created_at) }}</span
                >
              </div>

              <button
                @click="copyToClipboard"
                class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white rounded-xl font-bold text-sm hover:bg-primary/90 transition-all active:scale-95 cursor-pointer shadow-lg shadow-primary/20"
              >
                <Check v-if="isCopied" :size="18" />
                <Copy v-else :size="18" />
                {{ isCopied ? 'Copied to Clipboard' : 'Copy Content' }}
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<style scoped>
/* 1. Backdrop Fade (Standard) */
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.4s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

/* 2. Inner Content Box (Layered Motion) */
/* When parent starts entering, wait a tiny bit then slide/scale the box */
.modal-fade-enter-active .modal-container {
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  transition-delay: 0.1s;
}

.modal-fade-leave-active .modal-container {
  transition: all 0.25s ease-in;
}

/* Mobile: Slide up from bottom | Desktop: Subtle scale/slide */
.modal-fade-enter-from .modal-container {
  opacity: 0;
  transform: translateY(40px) scale(0.98);
}

@media (min-width: 640px) {
  .modal-fade-enter-from .modal-container {
    transform: translateY(20px) scale(0.95);
  }
}

.modal-fade-leave-to .modal-container {
  opacity: 0;
  transform: translateY(20px) scale(0.98);
}

/* Custom Scrollbar Styling */
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: #334155;
}
</style>
