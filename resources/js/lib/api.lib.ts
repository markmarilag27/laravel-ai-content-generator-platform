import httpClient from './http-client.lib';
import type {
  ILoginPayload,
  ILoginResponse,
  IMeResponse,
  IBaseResponse,
  // Brand Voice Types
  IExtractVoiceProfilePayload,
  IExtractVoiceProfileListResponse, // GET /
  IGetExtractVoiceProfileResponse, // GET /{id}
  IExtractVoiceProfileActionResponse, // POST & PATCH
  // Generation & Campaigns
  IGenerateContentPayload,
  IGenerateContentResponse,
  ICreateCampaignPayload,
  ICampaignResponse,
  IListContentResponse,
  IPaginatedResponse,
  ICampaign,
  ICampaignItem,
} from '@/types';

/**
 * Auth Module
 */
export const authApi = {
  login: (payload: ILoginPayload) => httpClient.post<ILoginResponse>('/auth/login', payload),

  logout: () => httpClient.post<IBaseResponse>('/auth/logout'),

  me: () => httpClient.get<IMeResponse>('/auth/me'),
};

/**
 * Brand Voice Module (api/brand-voice/extract)
 */
export const brandVoiceApi = {
  // GET /api/brand-voice/extract
  list: (page: number = 1) =>
    httpClient.get<IExtractVoiceProfileListResponse>(`/brand-voice/extract?page=${page}`),

  // POST /api/brand-voice/extract
  store: (payload: IExtractVoiceProfilePayload) =>
    httpClient.post<IExtractVoiceProfileActionResponse>('/brand-voice/extract', payload),

  // GET /api/brand-voice/extract/{profile}
  show: (profileId: string) =>
    httpClient.get<IGetExtractVoiceProfileResponse>(`/brand-voice/extract/${profileId}`),

  // PATCH /api/brand-voice/extract/{profile}
  update: (profileId: string, payload: Partial<IExtractVoiceProfilePayload>) =>
    httpClient.patch<IExtractVoiceProfileActionResponse>(
      `/brand-voice/extract/${profileId}`,
      payload
    ),

  // DELETE /api/brand-voice/extract/{profile}
  destroy: (profileId: string) =>
    httpClient.delete<IBaseResponse>(`/brand-voice/extract/${profileId}`),

  /**
   * List
   * GET /api/brand-voice/contents
   */
  listContent: (page: number = 1) =>
    httpClient.get<IListContentResponse>(`/brand-voice/contents?page=${page}`),

  /**
   * Content Generation
   * POST /api/brand-voice/generate/{profile}
   */
  generate: (profileId: string, payload: IGenerateContentPayload) =>
    httpClient.post<IGenerateContentResponse>(`/brand-voice/generate/${profileId}`, payload),
};

/**
 * Campaigns Module (/api/campaigns)
 */
export const campaignsApi = {
  /**
   * List all campaigns
   * GET /api/campaigns
   */
  index: () => httpClient.get<IPaginatedResponse<ICampaign>>('/campaigns'),

  /**
   * Store a new campaign for a specific profile
   * POST /api/campaigns/{profileId}
   */
  store: (profileId: string, payload: ICreateCampaignPayload) =>
    httpClient.post<ICampaignResponse>(`/campaigns/${profileId}`, payload),

  /**
   * Store a new campaign for a specific profile
   * GET /api/campaigns/{campaignId}
   */
  show: (campaignId: string) => httpClient.get<ICampaignResponse>(`/campaigns/${campaignId}`),

  /**
   * Delete a specific campaign
   * DELETE /api/campaigns/{campaignId}
   */
  destroy: (campaignId: string) => httpClient.delete(`/campaigns/${campaignId}`),

  /**
   * List of campaign items
   * GET /api/campaigns/{campaignId}/items
   */
  listItems: (campaignId: string, page: number = 1) =>
    httpClient.get<IPaginatedResponse<ICampaignItem>>(
      `/campaigns/${campaignId}/items?page=${page}`
    ),
};
