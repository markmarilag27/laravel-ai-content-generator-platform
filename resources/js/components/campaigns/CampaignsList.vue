<script setup lang="ts">
import type { ICampaign } from '@/types';
import { Rocket } from 'lucide-vue-next';
import CampaignRow from './CampaignRow.vue';

defineProps<{
  campaigns: ICampaign[];
}>();

const emit = defineEmits<{
  (e: 'delete', id: string): void;
  (e: 'view', id: string): void;
}>();
</script>

<template>
  <div
    class="w-full bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800/60 shadow-sm overflow-hidden"
  >
    <div
      class="hidden md:grid grid-cols-12 px-6 py-4 bg-slate-50/50 dark:bg-slate-800/40 border-b border-slate-200 dark:border-slate-800"
    >
      <div
        class="col-span-5 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest"
      >
        Campaign Details
      </div>
      <div
        class="col-span-2 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest"
      >
        Deadline
      </div>
      <div
        class="col-span-2 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest text-center"
      >
        Status
      </div>
      <div
        class="col-span-2 text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest"
      >
        Progress
      </div>
      <div
        class="col-span-1 text-right text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest"
      >
        Action
      </div>
    </div>

    <div class="w-full divide-y divide-slate-100 dark:divide-slate-800/60">
      <CampaignRow
        v-for="campaign in campaigns"
        :key="campaign.public_id"
        :item="campaign"
        @delete="emit('delete', campaign.public_id)"
        @view="emit('view', campaign.public_id)"
      />

      <div
        v-if="campaigns.length === 0"
        class="w-full flex flex-col items-center justify-center p-20 text-center"
      >
        <div
          class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4"
        >
          <Rocket class="text-slate-300 dark:text-slate-600" :size="32" />
        </div>
        <h3 class="text-slate-900 dark:text-white font-medium">No campaigns found</h3>
        <p class="text-sm text-slate-500 dark:text-slate-500 max-w-[240px] mt-1">
          Start your first campaign to generate high-quality AI content at scale.
        </p>
      </div>
    </div>
  </div>
</template>
