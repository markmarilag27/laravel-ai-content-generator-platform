import { useQuery, useMutation } from '@tanstack/vue-query';
import { queryClient } from '@/lib/query-client.lib';
import { brandVoiceApi } from '@/lib/api.lib';
import { QUERY_KEYS } from '@/lib/constant.lib';
import type { IExtractVoiceProfilePayload, IGenerateContentPayload } from '@/types';
import { ref, type Ref } from 'vue';

export function useBrandVoices() {
  /**
   * 1. List Fetching (Paginated)
   */
  const useListQuery = (page: Ref<number>) => {
    return useQuery({
      queryKey: [QUERY_KEYS.brandVoices, { page }],
      queryFn: () => brandVoiceApi.list(page.value),
    });
  };

  /**
   * 2. Single Record Fetching
   */
  const useDetailQuery = (profileId: Ref<string | null> | string) => {
    const id = ref(profileId);
    return useQuery({
      queryKey: [QUERY_KEYS.brandVoice, id],
      queryFn: () => brandVoiceApi.show(id.value!),
      enabled: () => !!id.value,
    });
  };

  /**
   * 3. Create Mutation
   */
  const useCreateMutation = () => {
    return useMutation({
      mutationFn: (payload: IExtractVoiceProfilePayload) => brandVoiceApi.store(payload),
      onSuccess: () => {
        // Invalidate list to show new entry
        queryClient.invalidateQueries({ queryKey: [QUERY_KEYS.brandVoices] });
      },
    });
  };

  /**
   * 4. Update Mutation
   */
  const useUpdateMutation = (profileId: string) => {
    return useMutation({
      mutationFn: (payload: Partial<IExtractVoiceProfilePayload>) =>
        brandVoiceApi.update(profileId, payload),
      onSuccess: (response) => {
        // Invalidate specific detail and the list
        queryClient.invalidateQueries({ queryKey: [QUERY_KEYS.brandVoice, profileId] });
        queryClient.invalidateQueries({ queryKey: [QUERY_KEYS.brandVoices] });
      },
    });
  };

  /**
   * 5. Delete Mutation
   */
  const useDeleteMutation = () => {
    return useMutation({
      mutationFn: (profileId: string) => brandVoiceApi.destroy(profileId),
      onSuccess: () => {
        queryClient.invalidateQueries({ queryKey: [QUERY_KEYS.brandVoices] });
      },
    });
  };

  /**
   * 6. List Content Mutation
   */
  const useListContentQuery = (page: Ref<number>) => {
    return useQuery({
      queryKey: [QUERY_KEYS.content, { page }],
      queryFn: () => brandVoiceApi.listContent(page.value),
    });
  };

  /**
   * 7. Generate Content Mutation
   */
  const useGenerateMutation = (page: Ref<number>) => {
    return useMutation({
      mutationFn: (payload: IGenerateContentPayload) => brandVoiceApi.generate(payload.id, payload),
      onSuccess: () => {
        queryClient.invalidateQueries({ queryKey: [QUERY_KEYS.currentUser] });
        queryClient.invalidateQueries({ queryKey: [QUERY_KEYS.content, { page }] });
      },
    });
  };

  return {
    useListQuery,
    useDetailQuery,
    useCreateMutation,
    useUpdateMutation,
    useDeleteMutation,
    useListContentQuery,
    useGenerateMutation,
  };
}
