import { defineStore } from 'pinia';

type AuthStoreState = {
  user: object | null;
};

export const useAuthStore = defineStore('auth', {
  state: (): AuthStoreState => ({
    user: null,
  }),
});
