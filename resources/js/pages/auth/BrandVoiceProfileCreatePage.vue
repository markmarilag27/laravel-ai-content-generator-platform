<script setup lang="ts">
import { useRouter } from 'vue-router';
import { useBrandVoices } from '@/composables/useBrandVoices';
import { useForm } from '@/composables/useForm';
import BrandVoiceForm from '@/components/brand-voice/BrandVoiceForm.vue';
import type { IExtractVoiceProfilePayload } from '@/types';

const router = useRouter();

const { useCreateMutation } = useBrandVoices();
const createMutation = useCreateMutation();

const { submit, errors, clearError } = useForm<IExtractVoiceProfilePayload>({
  name: '',
  samples: ['', '', ''],
});

const handleCreate = async (formData: IExtractVoiceProfilePayload) => {
  try {
    await submit({
      ...createMutation,
      mutateAsync: () => createMutation.mutateAsync(formData),
    });

    router.push({ name: 'BrandVoiceProfile' });
  } catch (err) {
    console.error('Failed to create brand voice profile:', err);
  }
};
</script>

<template>
  <div class="max-w-3xl mx-auto py-10 px-4">
    <div class="mb-8">
      <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
        Create Brand Voice Profile
      </h1>
      <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
        Establish a consistent tone for your generated content by providing reference samples.
      </p>
    </div>

    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="transform -translate-y-2 opacity-0"
      enter-to-class="transform translate-y-0 opacity-100"
    >
      <div
        v-if="errors.general"
        class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-medium flex items-center gap-3"
      >
        <div class="size-2 rounded-full bg-red-500 animate-pulse"></div>
        {{ errors.general }}
      </div>
    </Transition>

    <BrandVoiceForm
      :is-submitting="createMutation.isPending.value"
      :errors="errors"
      @submit="handleCreate"
      @clear-error="clearError"
      @cancel="router.back()"
    />
  </div>
</template>
