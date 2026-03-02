import httpClient from './http-client.lib';
import type {
  ILoginPayload, ILoginResponse, IMeResponse,
  IExtractVoiceProfilePayload, IExtractVoiceProfileResponse,
  IGenerateContentPayload, IGenerateContentResponse,
  ICreateCampaignPayload, ICampaignResponse,
  IBaseResponse
} from '@/types';

/**
 * Auth Module
 */
export const authApi = {
  login: (payload: ILoginPayload) =>
    httpClient.post<ILoginResponse>('/auth/login', payload),

  logout: () =>
    httpClient.post<IBaseResponse>('/auth/logout'),

  me: () =>
    httpClient.get<IMeResponse>('/auth/me'),
};

/**
 * Brand Voice Module
 */
export const brandVoiceApi = {
  extract: (payload: IExtractVoiceProfilePayload) =>
    httpClient.post<IExtractVoiceProfileResponse>('/brand-voice/extract', payload),

  generate: (profileId: string, payload: IGenerateContentPayload) =>
    httpClient.post<IGenerateContentResponse>(`/brand-voice/generate/${profileId}`, payload),
};

/**
 * Campaigns Module
 */
export const campaignsApi = {
  store: (profileId: string, payload: ICreateCampaignPayload) =>
    httpClient.post<ICampaignResponse>(`/campaigns/${profileId}`, payload),
};
