import { ITimestamps, TCampaignStatus, TISO8601String, TMetadata, TPlanName, TUserRole } from "./shared.type";

export interface IUser extends ITimestamps {
  public_id: string;
  name: string;
  email: string;
  email_verified_at: TISO8601String | null;
  // Relationships
  workspace?: IWorkspace | null;
  workspaces?: IWorkspace[];
  pivot?: IWorkspacePivot;
}

export interface IWorkspacePivot {
  role: TUserRole;
  created_at: TISO8601String;
  updated_at: TISO8601String;
}

export interface IWorkspace extends ITimestamps {
  public_id: string;
  name: string;
  // Relationships
  plan?: IPlan;
  users?: IUser[];
  brand_voice_profiles?: IBrandVoiceProfile[];
  campaigns?: ICampaign[];
}

export interface IPlan extends ITimestamps {
  public_id: string;
  name: TPlanName;
  monthly_credits: number;
}

export interface IBrandVoiceProfile extends ITimestamps {
  public_id: string;
  name: string;
  profile: TMetadata; // jsonb cast
}

export interface ICampaign extends ITimestamps {
  public_id: string;
  title: string;
  brief: TMetadata;
  deadline: TISO8601String | null;
  status: TCampaignStatus;
  // Relationships
  brand_voice_profile?: IBrandVoiceProfile;
  campaign_items?: ICampaignItem[];
}

export interface ICampaignItem extends ITimestamps {
  public_id: string;
  content_type: string;
  topic: string;
  word_count: number;
  output: TMetadata | null;
  tokens_used: number;
  status: TCampaignStatus;
  retry_count: number;
  error_message: string | null;
}

export interface ICreditLedger extends ITimestamps {
  public_id: string;
  amount: number;
  type: string;
  description: string | null;
  metadata: TMetadata | null;
  reference_type?: string;
  reference_id?: number;
}
