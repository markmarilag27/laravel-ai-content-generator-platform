export type TISO8601String = string;

export enum TPlanName {
  Free = 'free',
  Pro = 'pro',
  Enterprise = 'enterprise',
}

export enum TUserRole {
  Admin = 'admin',
  Member = 'member',
}

export enum TCampaignStatus {
  Pending = 'pending',
  Processing = 'processing',
  Completed = 'completed',
  Failed = 'failed',
}

export type TMetadata = Record<string, any>;

export interface ITimestamps {
  created_at: TISO8601String;
  updated_at: TISO8601String;
  deleted_at?: TISO8601String | null;
}
