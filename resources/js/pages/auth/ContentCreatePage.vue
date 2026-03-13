<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useBrandVoices } from '@/composables/useBrandVoices';
import { useForm } from '@/composables/useForm';
import type { IGenerateContentPayload } from '@/types';

const router = useRouter();
const { useListQuery, useGenerateMutation } = useBrandVoices();
const { data: voicesData } = useListQuery(ref(1));
const profiles = computed(() => voicesData.value?.data.data ?? []);

const page = ref(1);

const selectedProfileId = ref('');
const { form, errors, submit, clearError } = useForm<IGenerateContentPayload>({
  id: '',
  topic: '',
  content_type: '',
  word_count: 500,
});

const generateMutation = useGenerateMutation(page);

const handleGenerate = async () => {
  if (!selectedProfileId.value) {
    errors.value.brand_voice_profile_id = 'Please select a brand voice profile';
    return;
  }

  try {
    await submit({
      ...generateMutation,
      mutateAsync: (payload: IGenerateContentPayload) =>
        generateMutation.mutateAsync({
          ...payload,
          id: selectedProfileId.value,
        }),
    } as any);

    await router.push({ name: 'Contents' });
  } catch (err) {
    console.error('Generation failed:', err);
  }
};
</script>

<template>
  <div class="max-w-3xl mx-auto py-10 px-4">
    <div class="mb-10 text-center">
      <h1 class="text-4xl font-bold tracking-tight text-slate-900 dark:text-white">
        Generate Content
      </h1>
      <p class="mt-3 text-slate-600 dark:text-slate-400">
        Fill out the details below to let AI craft your next piece of content.
      </p>
    </div>

    <div
      v-if="errors.general"
      class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-medium"
    >
      {{ errors.general }}
    </div>

    <div
      class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 md:p-8"
    >
      <form @submit.prevent="handleGenerate" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300"
              >Brand Voice Profile</label
            >
            <select
              v-model="selectedProfileId"
              @change="clearError('brand_voice_profile_id')"
              class="w-full h-12 rounded-xl border border-slate-300 bg-white px-4 text-sm focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-900 outline-none cursor-pointer"
              :class="{ '!border-red-500': errors.brand_voice_profile_id }"
            >
              <option disabled value="">Select a profile...</option>
              <option v-for="p in profiles" :key="p.public_id" :value="p.public_id">
                {{ p.name }}
              </option>
            </select>
            <p v-if="errors.brand_voice_profile_id" class="text-xs text-red-500">
              {{ errors.brand_voice_profile_id }}
            </p>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300"
              >Content Type</label
            >
            <select
              v-model="form.content_type"
              @change="clearError('content_type')"
              class="w-full h-12 rounded-xl border border-slate-300 bg-white px-4 text-sm focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-900 outline-none cursor-pointer"
              :class="{ '!border-red-500': errors.content_type }"
            >
              <option disabled value="">Select type...</option>
              <option value="linkedin">LinkedIn Post</option>
              <option value="blog">Blog Article</option>
              <option value="twitter">Twitter Thread</option>
              <option value="email">Email Newsletter</option>
            </select>
            <p v-if="errors.content_type" class="text-xs text-red-500">{{ errors.content_type }}</p>
          </div>
        </div>

        <div class="space-y-2">
          <label class="text-sm font-semibold text-slate-700 dark:text-slate-300"
            >Topic & Brief</label
          >
          <textarea
            v-model="form.topic"
            @input="clearError('topic')"
            placeholder="Describe what you want to write about..."
            class="w-full min-h-[160px] rounded-xl border border-slate-300 bg-white p-4 text-sm focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-900 outline-none resize-y"
            :class="{ '!border-red-500': errors.topic }"
          ></textarea>
          <p v-if="errors.topic" class="text-xs text-red-500">{{ errors.topic }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
          <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300"
              >Word Count</label
            >
            <input
              v-model.number="form.word_count"
              type="number"
              step="50"
              min="50"
              max="2000"
              class="w-full h-12 rounded-xl border border-slate-300 bg-white px-4 text-sm focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-900 outline-none"
            />
          </div>

          <button
            type="submit"
            :disabled="generateMutation.isPending.value"
            class="h-12 w-full cursor-pointer bg-primary text-white rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all flex items-center justify-center gap-2"
          >
            <span v-if="generateMutation.isPending.value" class="flex items-center gap-2">
              <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                  fill="none"
                ></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
              Crafting Content...
            </span>
            <span v-else>Generate Content</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
