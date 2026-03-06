import { TISO8601String } from './shared.type';

export interface ILoginPayload {
  email: string;
  password: string;
  remember?: boolean;
}

export interface IExtractVoiceProfilePayload {
  name: string;
  samples: string[];
}

export interface IGenerateContentPayload {
  id: string;
  topic: string;
  content_type: string;
  word_count: number;
}

export type TCampaignBriefQuantities = Record<string, number>;
export type TCampaignBriefWordCounts = Record<string, number>;

export interface ICampaignBrief {
  goal: string;
  topic: string;
  quantities: TCampaignBriefQuantities;
  word_counts: TCampaignBriefWordCounts;
}

export interface ICreateCampaignPayload {
  title: string;
  deadline: TISO8601String;
  brand_voice_profile_id: string;
  brief: ICampaignBrief;
}
