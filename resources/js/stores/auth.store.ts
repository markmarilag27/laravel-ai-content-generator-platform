import { defineStore } from 'pinia';
import { useQuery } from '@tanstack/vue-query';
import { computed } from 'vue';
import { authApi } from '@/lib/api.lib';
import { queryClient } from '@/lib/query-client.lib';
import type { IUser, ILoginPayload } from '@/types';
import { QUERY_KEYS } from '@/lib/constant.lib';
import { AxiosError } from 'axios';

export const useAuthStore = defineStore('auth', () => {
  const {
    data: user,
    isFetched,
    refetch,
  } = useQuery({
    queryKey: [QUERY_KEYS.currentUser],
    queryFn: async () => {
      try {
        const response = await authApi.me();
        return response.data as IUser;
      } catch (e: unknown) {
        if (e instanceof AxiosError && e.response?.status === 401) return null;
        throw e;
      }
    },
    staleTime: 1000 * 60 * 60,
    retry: false,
  });

  const initialized = computed(() => isFetched.value);
  const isAuthenticated = computed(() => !!user.value);

  async function login(credentials: ILoginPayload) {
    await authApi.login(credentials);
    await refetch();
  }

  async function logout() {
    try {
      await authApi.logout();
    } finally {
      queryClient.clear();
      queryClient.setQueryData([QUERY_KEYS.currentUser], null);
    }
  }

  async function fetchUser() {
    await refetch();
  }

  return {
    user,
    initialized,
    isAuthenticated,
    login,
    logout,
    fetchUser,
  };
});
