import { IUser } from "./model.type";
import { ITimestamps, TCampaignStatus } from "./shared.type";

export interface IErrorResponse {
  message: string
  errors?: Record<string, string[]>
}

export type TValidationErrors = Record<string, string[]>;

export interface IBaseResponse {
  message: string;
}

export interface IDataResponse<T> extends IBaseResponse {
  data: T;
}

export interface ILoginResponse extends IBaseResponse {
  user: IUser
}

export interface IMeResponse extends IUser {}

export type TVoiceProfileDetails = {
  tone: string;
  formality: number;
  patterns: string[];
  persona: string;
};

export interface IVoiceProfileData extends ITimestamps {
  public_id: string;
  name: string;
  profile: TVoiceProfileDetails;
}

export interface IExtractVoiceProfileResponse extends IDataResponse<IVoiceProfileData> {}

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

export interface ICampaignQueuedData {
  campaign_id: string;
  status: TCampaignStatus;
}

export interface ICampaignResponse extends IDataResponse<ICampaignQueuedData> {}
