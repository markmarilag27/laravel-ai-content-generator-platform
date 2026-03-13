<script setup lang="ts">
import { ref, computed } from 'vue';
import {
  Sparkles,
  Loader2,
  Calendar,
  Target,
  FileText,
  Type,
  PlusCircle,
  Trash2,
  AlertCircle,
  UserCircle,
} from 'lucide-vue-next';

const props = defineProps<{
  isSubmitting?: boolean;
  errors?: Record<string, string>;
  profiles: any[];
  modelValue: string;
}>();

const emit = defineEmits(['submit', 'cancel', 'clear-error', 'update:modelValue']);

const selectedProfileId = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val),
});

const deliverables = ref([
  { type: 'LinkedIn Post', quantity: 3, words: 150 },
  { type: 'Twitter Thread', quantity: 2, words: 50 },
]);

const form = ref({
  title: '',
  deadline: '',
  brief: {
    goal: '',
    topic: '',
  },
});

const addDeliverable = () => {
  if (deliverables.value.length < 10) {
    deliverables.value.push({ type: '', quantity: 1, words: 100 });
  }
};

const removeDeliverable = (index: number) => {
  if (deliverables.value.length > 1) {
    deliverables.value.splice(index, 1);
  }
};

const handleSubmit = () => {
  const quantities: Record<string, number> = {};
  const word_counts: Record<string, number> = {};

  deliverables.value.forEach((item) => {
    if (item.type) {
      quantities[item.type] = item.quantity;
      word_counts[item.type] = item.words;
    }
  });

  emit('submit', {
    ...form.value,
    brand_voice_profile_id: selectedProfileId.value,
    brief: {
      ...form.value.brief,
      quantities,
      word_counts,
    },
  });
};

const isFormValid = computed(() => {
  return (
    selectedProfileId.value !== '' &&
    form.value.title.trim().length > 0 &&
    form.value.brief.goal.trim().length >= 10 &&
    form.value.brief.topic.trim().length >= 10 &&
    deliverables.value.every((d) => d.type.trim() !== '')
  );
});
</script>

<template>
  <form @submit.prevent="handleSubmit" class="space-y-6">
    <div
      class="bg-white dark:bg-brand-dark rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 md:p-8 space-y-8"
    >
      <div class="space-y-2 max-w-md">
        <label
          class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2"
        >
          <UserCircle :size="16" class="text-slate-400" /> Brand Voice Profile
        </label>
        <select
          v-model="selectedProfileId"
          @change="emit('clear-error', 'brand_voice_profile_id')"
          class="w-full h-12 rounded-xl border border-slate-300 bg-white px-4 text-sm focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-brand-dark dark:text-slate-100 outline-none cursor-pointer"
          :class="{ '!border-red-500': errors?.brand_voice_profile_id }"
        >
          <option disabled value="">Select a profile...</option>
          <option v-for="p in profiles" :key="p.public_id" :value="p.public_id">
            {{ p.name }}
          </option>
        </select>
        <p v-if="errors?.brand_voice_profile_id" class="text-xs text-red-500 font-medium mt-1">
          {{ errors.brand_voice_profile_id }}
        </p>
      </div>

      <hr class="border-slate-100 dark:border-slate-800" />

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-2">
          <label
            class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2"
          >
            <Type :size="16" class="text-slate-400" /> Campaign Title
          </label>
          <input
            v-model="form.title"
            type="text"
            placeholder="e.g., Spring Launch 2026"
            class="flex h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-brand-dark dark:text-slate-100 outline-none"
            :class="{ '!border-red-500': errors?.title }"
          />
        </div>

        <div class="space-y-2">
          <label
            class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2"
          >
            <Calendar :size="16" class="text-slate-400" /> Deadline
          </label>
          <input
            v-model="form.deadline"
            type="datetime-local"
            class="flex h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-brand-dark dark:text-slate-100 outline-none"
          />
        </div>
      </div>

      <hr class="border-slate-100 dark:border-slate-800" />

      <div class="space-y-6">
        <div class="space-y-2">
          <label
            class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2"
          >
            <Target :size="16" class="text-slate-400" /> Goal
          </label>
          <textarea
            v-model="form.brief.goal"
            rows="2"
            placeholder="Min 10 characters..."
            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-brand-dark dark:text-slate-100 outline-none"
            :class="{ '!border-red-500': errors?.['brief.goal'] }"
          ></textarea>
        </div>

        <div class="space-y-2">
          <label
            class="text-sm font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-2"
          >
            <FileText :size="16" class="text-slate-400" /> Topic
          </label>
          <textarea
            v-model="form.brief.topic"
            rows="3"
            placeholder="Describe what we are promoting..."
            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-brand-dark dark:text-slate-100 outline-none"
            :class="{ '!border-red-500': errors?.['brief.topic'] }"
          ></textarea>
        </div>
      </div>

      <hr class="border-slate-100 dark:border-slate-800" />

      <div class="space-y-4">
        <div class="flex justify-between items-center">
          <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400">
            Content Deliverables
          </h3>
          <span class="text-[10px] font-bold bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded">
            {{ deliverables.length }} / 10
          </span>
        </div>

        <div
          v-for="(item, index) in deliverables"
          :key="index"
          class="p-4 bg-brand-light dark:bg-slate-800/40 rounded-xl border border-slate-100 dark:border-slate-800 space-y-4"
        >
          <div class="flex items-center gap-4">
            <div class="flex-1 space-y-1">
              <input
                v-model="item.type"
                placeholder="Content Type (e.g. Email)"
                class="w-full bg-transparent font-semibold text-slate-900 dark:text-slate-100 border-b border-transparent focus:border-primary outline-none"
              />
            </div>
            <button
              v-if="deliverables.length > 1"
              @click="removeDeliverable(index)"
              type="button"
              class="text-red-400 hover:text-red-500 transition-colors"
            >
              <Trash2 :size="18" />
            </button>
          </div>

          <div class="flex flex-wrap gap-6">
            <div class="flex flex-col">
              <span class="text-[10px] uppercase text-slate-500 font-bold mb-1"
                >Quantity (1-7)</span
              >
              <input
                type="number"
                v-model.number="item.quantity"
                min="1"
                max="7"
                class="w-20 bg-white dark:bg-brand-dark border border-slate-200 dark:border-slate-700 rounded-lg px-2 py-1 text-sm outline-none focus:ring-1 focus:ring-primary"
              />
            </div>
            <div class="flex flex-col">
              <span class="text-[10px] uppercase text-slate-500 font-bold mb-1"
                >Words (50-2000)</span
              >
              <input
                type="number"
                v-model.number="item.words"
                min="50"
                max="2000"
                class="w-24 bg-white dark:bg-brand-dark border border-slate-200 dark:border-slate-700 rounded-lg px-2 py-1 text-sm outline-none focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>
        </div>

        <button
          v-if="deliverables.length < 10"
          @click="addDeliverable"
          type="button"
          class="w-full py-4 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-xl text-slate-500 hover:text-primary hover:border-primary hover:bg-primary/5 transition-all flex items-center justify-center gap-2"
        >
          <PlusCircle :size="18" />
          <span class="text-sm font-semibold">Add Content Type</span>
        </button>
      </div>
    </div>

    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-2">
      <button
        @click="emit('cancel')"
        type="button"
        class="text-slate-500 cursor-pointer hover:text-brand-dark dark:hover:text-white font-semibold transition-colors"
      >
        Cancel
      </button>

      <div class="flex flex-col items-end gap-2">
        <button
          type="submit"
          :disabled="isSubmitting || !isFormValid"
          class="cursor-pointer bg-primary hover:opacity-90 disabled:opacity-40 disabled:cursor-not-allowed text-white px-10 py-3.5 rounded-xl font-bold flex items-center gap-2 transition-all shadow-lg shadow-primary/20 active:scale-[0.98]"
        >
          <Loader2 v-if="isSubmitting" class="animate-spin size-5" />
          <Sparkles v-else :size="18" />
          <span>{{ isSubmitting ? 'Analyzing & Generating...' : 'Create Campaign' }}</span>
        </button>
        <p v-if="!isFormValid" class="text-[11px] text-amber-600 flex items-center gap-1">
          <AlertCircle :size="12" /> Check title, goals (10+ chars), and content types.
        </p>
      </div>
    </div>
  </form>
</template>
