import {
  ITimestamps,
  TCampaignStatus,
  TISO8601String,
  TMetadata,
  TPlanName,
  TUserRole,
} from './shared.type';

/**
 * Represents the join table data between a User and a Workspace
 */
export interface IWorkspacePivot {
  role: TUserRole; // Mapped from withPivot('role')
  created_at: TISO8601String; // Mapped from withTimestamps()
  updated_at: TISO8601String; // Mapped from withTimestamps()
}

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

export interface IWorkspace extends ITimestamps {
  public_id: string;
  name: string;
  credits_remaining: number;
  // Relationships
  plan?: IPlan;
  users?: IUser[];
  brand_voice_profiles?: IBrandVoiceProfile[];
}

export interface IPlan extends ITimestamps {
  public_id: string;
  name: TPlanName;
  monthly_credits: number;
}

export interface IBrandVoiceProfile extends ITimestamps {
  public_id: string;
  name: string;
  samples: string[];
  profile: TMetadata;
}

export interface ICampaign extends ITimestamps {
  public_id: string;
  title: string;
  status: TCampaignStatus;
  deadline: TISO8601String | null;
  // Relationships
  brand_voice_profile?: IBrandVoiceProfile;
  items?: ICampaignItem[];
}

export interface ICampaignItem extends ITimestamps {
  public_id: string;
  content_type: string;
  topic: string;
  word_count: number;
  output: TMetadata | null;
  tokens_used: number;
  status: TCampaignStatus;
  error_message: string | null;
}

export interface ICreditLedger extends ITimestamps {
  public_id: string;
  amount: number;
  type: string;
  description: string | null;
  metadata: TMetadata | null;
}

export interface IContent extends ITimestamps {
  public_id: string;
  body: string;
  tokens_used: number;

  // Relationships
  workspace?: IWorkspace;
  brand_voice_profile?: IBrandVoiceProfile;
}
