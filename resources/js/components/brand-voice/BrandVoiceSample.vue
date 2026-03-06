<script setup lang="ts">
import { computed } from 'vue';
import { Trash2, AlertCircle } from 'lucide-vue-next';

const props = defineProps<{
  modelValue: string;
  index: number;
  showRemove: boolean;
  error?: string;
}>();

const emit = defineEmits(['update:modelValue', 'remove']);

const wordCount = computed(() => {
  if (!props.modelValue) return 0;
  return props.modelValue.trim().split(/\s+/).length;
});
</script>

<template>
  <div
    class="flex flex-col gap-2 bg-slate-50 dark:bg-brand-dark/50 p-4 rounded-xl border transition-colors relative group"
    :class="
      error ? 'border-red-200 dark:border-red-900/50' : 'border-slate-100 dark:border-slate-800'
    "
  >
    <div class="flex justify-between items-center mb-1">
      <label class="text-sm font-semibold text-slate-700 dark:text-slate-300">
        Sample {{ index + 1 }}
      </label>
      <button
        v-if="showRemove"
        @click="emit('remove')"
        type="button"
        class="text-xs cursor-pointer text-red-500 hover:text-red-600 flex items-center gap-1 transition-colors"
      >
        <Trash2 :size="14" />
        <span>Remove</span>
      </button>
    </div>

    <textarea
      :value="modelValue"
      @input="emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
      class="w-full rounded-xl bg-white dark:bg-slate-900 border px-4 py-3 text-sm transition-all outline-none min-h-[160px] resize-y"
      :class="
        error
          ? 'border-red-500 focus:ring-red-500'
          : 'border-slate-300 dark:border-slate-700 focus:border-primary focus:ring-1 focus:ring-primary'
      "
      placeholder="Paste a blog post, email, or article here..."
    ></textarea>

    <div class="flex justify-between items-center mt-1">
      <div class="text-xs text-slate-500 dark:text-slate-400">{{ wordCount }} words</div>
      <p v-if="error" class="text-[11px] font-medium text-red-500 flex items-center gap-1">
        <AlertCircle :size="12" /> {{ error }}
      </p>
    </div>
  </div>
</template>
