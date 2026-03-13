import { ICampaign, IContent, IUser } from './model.type';
import { ITimestamps, TCampaignStatus } from './shared.type';

export interface IErrorResponse {
  message: string;
  errors?: Record<string, string[]>;
}

export type TValidationErrors = Record<string, string[]>;

export interface IBaseResponse {
  message: string;
}

export interface IResourceResponse<T> {
  data: T;
}

export interface IDataResponse<T> extends IBaseResponse {
  data: T;
}

export interface ILoginResponse extends IBaseResponse {
  user: IUser;
}

export interface IMeResponse extends IUser {}

export type TVoiceProfileDetails = {
  tone: string;
  persona: string;
  patterns: string[];
  formality: number;
};

export interface IVoiceProfileData extends ITimestamps {
  public_id: string;
  name: string;
  samples: string[];
  profile: TVoiceProfileDetails;
}

export type IExtractVoiceProfileListResponse = IPaginatedResponse<IVoiceProfileData>;
export type IGetExtractVoiceProfileResponse = IResourceResponse<IVoiceProfileData>;
export interface IExtractVoiceProfileActionResponse extends IDataResponse<IVoiceProfileData> {}

export interface IGeneratedContentDetails {
  content: string;
  tokens: number;
  attempts: number;
  final_score: number;
}

export interface IGenerateContentResponse extends IBaseResponse {
  content: IGeneratedContentDetails;
  remaining_balance: number;
}

export interface IListContentResponse extends IPaginatedResponse<IContent> {}

export interface ICampaignResponse extends IDataResponse<ICampaign> {}

export interface IPaginatedResponse<T> {
  data: T[];
  links: IPaginationLinks;
  meta: IPaginationMeta;
}

export interface IPaginationLinks {
  first: string;
  last: string | null;
  prev: string | null;
  next: string | null;
}

export interface IPaginationMeta {
  current_page: number;
  current_page_url: string;
  from: number | null;
  path: string;
  per_page: number;
  to: number | null;
}
