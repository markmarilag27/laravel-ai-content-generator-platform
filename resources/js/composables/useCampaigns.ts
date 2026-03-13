import { useQuery, useMutation } from '@tanstack/vue-query';
import { queryClient } from '@/lib/query-client.lib';
import { campaignsApi } from '@/lib/api.lib';
import { QUERY_KEYS } from '@/lib/constant.lib';
import type { ICreateCampaignPayload } from '@/types';
import { type Ref } from 'vue';

export function useCampaigns() {
  /**
   * List Fetching (Paginated)
   * GET /api/campaigns
   */
  const useListQuery = (page: Ref<number>) => {
    return useQuery({
      queryKey: [QUERY_KEYS.campaigns, { page }],
      queryFn: () => campaignsApi.index(), // Note: Update api.lib.ts to accept page if Laravel side is paginated
    });
  };

  /**
   * Create Mutation
   * POST /api/campaigns/{profileId}
   */
  const useCreateMutation = () => {
    return useMutation({
      mutationFn: ({
        profileId,
        payload,
      }: {
        profileId: string;
        payload: ICreateCampaignPayload;
      }) => campaignsApi.store(profileId, payload),
      onSuccess: () => {
        // Refresh the list to show the new campaign
        queryClient.invalidateQueries({ queryKey: [QUERY_KEYS.campaigns] });
      },
    });
  };

  /**
   * Fetch Campaign
   * GET /api/campaigns/{campaignId}
   */
  const useDetailQuery = (campaignId: Ref<string>) => {
    return useQuery({
      queryKey: [QUERY_KEYS.campaign, { campaignId }],
      queryFn: () => campaignsApi.show(campaignId.value!),
      enabled: () => !!campaignId.value,
    });
  };

  /**
   * Delete Mutation
   * DELETE /api/campaigns/{campaignId}
   */
  const useDeleteMutation = () => {
    return useMutation({
      mutationFn: (campaignId: string) => campaignsApi.destroy(campaignId),
      onSuccess: () => {
        // Refresh list after deletion
        queryClient.invalidateQueries({ queryKey: [QUERY_KEYS.campaigns] });
      },
    });
  };

  /**
   * List campaign items
   * GET /api/campaigns/{campaignId}/items
   */
  const useListItemsQuery = (campaignId: Ref<string>, page: Ref<number>) =>
    useQuery({
      queryKey: [QUERY_KEYS.campaignItems, { campaignId, page }],
      queryFn: () => campaignsApi.listItems(campaignId.value!, page.value),
      enabled: () => !!campaignId.value,
    });

  return {
    useListQuery,
    useCreateMutation,
    useDetailQuery,
    useDeleteMutation,
    useListItemsQuery,
  };
}
