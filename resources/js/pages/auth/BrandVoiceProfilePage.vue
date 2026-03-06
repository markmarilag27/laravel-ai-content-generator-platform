<script setup lang="ts">
import { ref, computed } from 'vue';
import { useBrandVoices } from '@/composables/useBrandVoices';
import AppLoader from '@/components/AppLoader.vue';
import BrandVoiceHeader from '@/components/brand-voice/BrandVoiceHeader.vue';
import BrandVoiceList from '@/components/brand-voice/BrandVoiceList.vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const currentPage = ref(1);
const { useListQuery, useDeleteMutation } = useBrandVoices();

const { data, isLoading, isError, error } = useListQuery(currentPage);

const profiles = computed(() => data.value?.data.data ?? []);

const { mutate: deleteProfile } = useDeleteMutation();

const handleCreate = async () => {
  await router.push({ name: 'BrandVoiceProfileCreate' });
};
const handleEdit = async (id: string) => {
  await router.push({ name: 'BrandVoiceProfileEdit', params: { id } });
};
const handleDelete = (id: string) => {
  if (confirm('Delete this profile?')) deleteProfile(id);
};
</script>

<template>
  <div class="bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
      <div class="flex flex-col gap-6">
        <BrandVoiceHeader @create="handleCreate" />

        <div v-if="isError" class="p-4 bg-red-50 text-red-700 rounded-lg border border-red-200">
          <p>Error: {{ error?.message }}</p>
        </div>

        <div v-if="isLoading" class="py-20">
          <AppLoader message="Fetching your brand voices..." />
        </div>

        <template v-else>
          <BrandVoiceList :profiles="profiles" @edit="handleEdit" @delete="handleDelete" />

          <div class="flex justify-between items-center px-2 text-xs text-slate-500">
            <p>Showing {{ profiles.length }} profile(s)</p>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>
