<script setup lang="ts">
import { ref, computed } from 'vue';
import { useCampaigns } from '@/composables/useCampaigns';
import { Plus } from 'lucide-vue-next';
import CampaignsList from '@/components/campaigns/CampaignsList.vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const page = ref(1);
const { useListQuery, useDeleteMutation } = useCampaigns();

const { data: response, isLoading, isError } = useListQuery(page);

const { mutate: deleteCampaign } = useDeleteMutation();

const campaigns = computed(() => response.value?.data?.data || []);

const handleDelete = (id: string) => {
  if (confirm('Are you sure you want to delete this campaign?')) {
    deleteCampaign(id);
  }
};

const handleView = async (id: string) => {
  await router.push({ name: 'CampaignDetail', params: { id } });
};

const handleCreate = async () => {
  await router.push({ name: 'CampaignCreate' });
};
</script>

<template>
  <main class="flex-1 px-4 md:px-8 py-8 flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div class="flex flex-col gap-1">
        <h1 class="tracking-tight text-3xl font-bold text-slate-900 dark:text-slate-100">
          Campaigns
        </h1>
        <p class="text-slate-500 dark:text-slate-400 text-base font-normal">
          Manage and track your content generation campaigns
        </p>
      </div>

      <button
        class="flex cursor-pointer items-center justify-center gap-2 rounded-xl h-11 px-6 bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition-all shadow-sm active:scale-95 w-full sm:w-auto"
        @click="handleCreate"
      >
        <Plus :size="18" />
        <span>Create Campaign</span>
      </button>
    </div>

    <div
      v-if="isError"
      class="p-8 text-center bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-900/30 rounded-2xl"
    >
      <p class="text-red-600 dark:text-red-400 font-medium">
        Failed to load campaigns. Please try again.
      </p>
    </div>

    <div v-else class="w-full">
      <div v-if="isLoading" class="space-y-4">
        <div class="h-16 w-full bg-slate-100 dark:bg-slate-800 animate-pulse rounded-2xl"></div>
        <div
          class="h-64 w-full bg-slate-50 dark:bg-slate-900/50 animate-pulse rounded-2xl border border-slate-200 dark:border-slate-800"
        ></div>
      </div>

      <CampaignsList v-else :campaigns="campaigns" @delete="handleDelete" @view="handleView" />
    </div>
  </main>
</template>
