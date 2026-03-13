<script setup lang="ts">
import { useRouter } from 'vue-router';
import { useCampaigns } from '@/composables/useCampaigns';
import { useForm } from '@/composables/useForm';
import CampaignForm from '@/components/campaigns/CampaignForm.vue';
import type { ICreateCampaignPayload } from '@/types';
import { computed, ref } from 'vue';
import { useBrandVoices } from '@/composables/useBrandVoices';

const router = useRouter();
const selectedProfileId = ref('');

const { useListQuery } = useBrandVoices();
const { data: voicesData } = useListQuery(ref(1));
const profiles = computed(() => voicesData.value?.data.data ?? []);
const { useCreateMutation } = useCampaigns();
const createMutation = useCreateMutation();

const { submit, errors, clearError } = useForm<ICreateCampaignPayload>({
  brand_voice_profile_id: '',
  title: '',
  deadline: '',
  brief: {
    goal: '',
    topic: '',
    quantities: {},
    word_counts: {},
  },
});

const handleCreate = async (formData: ICreateCampaignPayload) => {
  try {
    await submit({
      ...createMutation,
      mutateAsync: () =>
        createMutation
          .mutateAsync({ payload: formData, profileId: selectedProfileId.value })
          .then(({ data }) => {
            const campaignId = data.data.public_id;
            router.push({ name: 'CampaignDetail', params: { id: campaignId } });
          }),
    } as any);
  } catch (err) {
    console.error('Submission failed:', err);
  }
};
</script>

<template>
  <div class="max-w-4xl mx-auto py-10 px-4">
    <div class="mb-8">
      <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
        New Campaign
      </h1>
      <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
        Define your goal and content types. We'll use this information to generate the assets.
      </p>
    </div>

    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="transform -translate-y-2 opacity-0"
      enter-to-class="transform translate-y-0 opacity-100"
    >
      <div
        v-if="errors.general"
        class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm flex items-center gap-3"
      >
        <div class="size-2 rounded-full bg-red-500 animate-pulse"></div>
        {{ errors.general }}
      </div>
    </Transition>

    <CampaignForm
      v-model="selectedProfileId"
      :profiles="profiles"
      :is-submitting="createMutation.isPending.value"
      :errors="errors"
      @submit="handleCreate"
      @clear-error="clearError"
      @cancel="router.back()"
    />
  </div>
</template>
