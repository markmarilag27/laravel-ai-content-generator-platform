<script setup lang="ts">
import { computed, watch } from 'vue'; // Added watch
import { useRoute, useRouter } from 'vue-router';
import { useBrandVoices } from '@/composables/useBrandVoices';
import { useForm } from '@/composables/useForm';
import BrandVoiceForm from '@/components/brand-voice/BrandVoiceForm.vue';
import AppLoader from '@/components/AppLoader.vue';
import type { IExtractVoiceProfilePayload, IVoiceProfileData } from '@/types';

const route = useRoute();
const router = useRouter();
const profileId = computed(() => route.params.id as string);

const { useDetailQuery, useUpdateMutation } = useBrandVoices();
const { data: profile, isLoading, isError } = useDetailQuery(profileId.value);

const profileData = computed(() => profile.value?.data.data as IVoiceProfileData);

const updateMutation = useUpdateMutation(profileId.value);

const { form, submit, errors, clearError } = useForm<IExtractVoiceProfilePayload>({
  name: '',
  samples: ['', '', ''],
});

/**
 * 1. Reactive Sync
 * When profileData is loaded from the API, update the form state
 */
watch(
  profileData,
  (newData) => {
    if (newData) {
      form.value.name = newData.name;
      // Ensure we maintain the min 3 samples even if API returns fewer
      form.value.samples =
        newData.samples?.length >= 3
          ? [...newData.samples]
          : [...newData.samples, ...Array(3 - newData.samples.length).fill('')];
    }
  },
  { immediate: true }
);

const handleUpdate = async (formData: IExtractVoiceProfilePayload) => {
  try {
    await submit({
      ...updateMutation,
      mutateAsync: () => updateMutation.mutateAsync(formData),
    } as any);
    router.push({ name: 'BrandVoiceProfile' });
  } catch (err) {
    console.error('Update failed:', err);
  }
};
</script>

<template>
  <div class="max-w-3xl mx-auto py-10 px-4">
    <div class="mb-8">
      <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
        Edit Brand Voice Profile
      </h1>
      <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
        Refine your brand voice by updating your name or content samples.
      </p>
    </div>

    <div v-if="isLoading" class="py-20">
      <AppLoader message="Loading profile details..." />
    </div>

    <div
      v-else-if="isError"
      class="p-8 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800"
    >
      <p class="text-slate-500">Could not find this brand voice profile.</p>
      <button @click="router.back()" class="mt-4 text-primary font-semibold hover:underline">
        Go Back
      </button>
    </div>

    <template v-else>
      <div
        v-if="errors.general"
        class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-medium"
      >
        {{ errors.general }}
      </div>

      <BrandVoiceForm
        :initial-data="profileData"
        :is-submitting="updateMutation.isPending.value"
        :errors="errors"
        @submit="handleUpdate"
        @clear-error="clearError"
        @cancel="router.back()"
      />
    </template>
  </div>
</template>
