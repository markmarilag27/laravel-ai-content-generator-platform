<script setup lang="ts">
import { ref, computed } from 'vue';
import { PlusCircle, Sparkles, Loader2, AlertCircle } from 'lucide-vue-next';
import BrandVoiceSample from './BrandVoiceSample.vue';

const props = defineProps<{
  initialData?: { name: string; samples: string[] };
  isSubmitting?: boolean;
  errors?: Record<string, string>;
}>();

const emit = defineEmits(['submit', 'cancel', 'clear-error']);

const defaultSamples = ['', '', ''];
const form = ref({
  name: props.initialData?.name ?? '',
  samples: props.initialData?.samples?.length
    ? [...props.initialData.samples]
    : [...defaultSamples],
});

const addSample = () => {
  if (form.value.samples.length < 5) {
    form.value.samples.push('');
  }
};

const removeSample = (index: number) => {
  if (form.value.samples.length > 3) {
    form.value.samples.splice(index, 1);
  }
};

const handleInput = (field: string) => {
  emit('clear-error', field);
};

const isFormValid = computed(() => {
  const hasName = form.value.name.trim().length > 0;
  const allSamplesFilled = form.value.samples.every((s) => s.trim().length > 0);
  return hasName && allSamplesFilled && form.value.samples.length >= 3;
});
</script>

<template>
  <form @submit.prevent="emit('submit', form)" class="space-y-8">
    <div
      class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 md:p-8 space-y-8"
    >
      <div class="space-y-2">
        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">Profile Name</label>
        <input
          v-model="form.name"
          type="text"
          @input="handleInput('name')"
          placeholder="e.g., Casual Blog, Professional Emails"
          class="flex h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 transition-all placeholder:text-slate-400 focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-500 outline-none"
          required
          :class="{ '!border-red-500 !ring-red-500': errors?.name }"
        />
        <p v-if="errors?.name" class="text-xs font-medium text-red-500">{{ errors.name }}</p>
      </div>

      <hr class="border-slate-100 dark:border-slate-800" />

      <div class="space-y-6">
        <div class="flex justify-between items-end">
          <div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">Content Samples</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">
              Provide 3 to 5 samples (min. 300 words each recommended).
            </p>
          </div>
          <div
            class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-2 py-1 bg-slate-100 dark:bg-slate-800 rounded-md"
          >
            {{ form.samples.length }} / 5 Samples
          </div>
        </div>

        <BrandVoiceSample
          v-for="(sample, index) in form.samples"
          :key="index"
          v-model="form.samples[index]"
          :index="index"
          :show-remove="form.samples.length > 3"
          :error="errors?.[`samples.${index}`]"
          @remove="removeSample(index)"
          @update:model-value="handleInput(`samples.${index}`)"
        />

        <button
          v-if="form.samples.length < 5"
          @click="addSample"
          type="button"
          class="w-full cursor-pointer py-4 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-xl text-slate-500 hover:text-primary hover:border-primary hover:bg-primary/5 transition-all flex items-center justify-center gap-2 bg-slate-50/50"
        >
          <PlusCircle :size="18" />
          <span class="text-sm font-semibold">Add another sample (up to 5)</span>
        </button>
      </div>
    </div>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
      <button
        @click="emit('cancel')"
        type="button"
        class="text-slate-500 cursor-pointer hover:text-slate-900 dark:hover:text-slate-200 text-sm font-semibold transition-colors px-2"
      >
        Cancel
      </button>

      <div class="flex flex-col sm:items-end gap-2">
        <button
          type="submit"
          :disabled="isSubmitting || !isFormValid"
          class="bg-primary cursor-pointer hover:bg-primary/90 active:scale-[0.98] disabled:opacity-40 disabled:cursor-not-allowed disabled:active:scale-100 text-white px-8 py-3.5 rounded-xl font-bold text-sm flex items-center justify-center gap-2 transition-all shadow-lg shadow-primary/20"
        >
          <Loader2 v-if="isSubmitting" class="animate-spin size-5" />
          <Sparkles v-else :size="18" />
          <span>{{ isSubmitting ? 'Analyzing Voice...' : 'Extract & Save Profile' }}</span>
        </button>
        <p
          v-if="!isFormValid && form.name"
          class="text-[11px] text-amber-600 dark:text-amber-500 flex items-center gap-1"
        >
          <AlertCircle :size="12" /> Please fill out all 3 required samples.
        </p>
      </div>
    </div>
  </form>
</template>
