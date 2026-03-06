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
 * Campaigns Module
 * POST /api/campaigns/{profile}
 */
export const campaignsApi = {
  store: (profileId: string, payload: ICreateCampaignPayload) =>
    httpClient.post<ICampaignResponse>(`/campaigns/${profileId}`, payload),
};
