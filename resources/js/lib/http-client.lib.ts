import axios from 'axios';
import { queryClient } from './query-client.lib';
import { QUERY_KEYS } from './constant.lib';

const httpClient = axios.create({
  baseURL: '/api',
  timeout: 55000,
  withCredentials: true,
  withXSRFToken: true,
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
  },
});

httpClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      queryClient.setQueryData([QUERY_KEYS.currentUser], null);
      queryClient.clear();
    }
    return Promise.reject(error);
  }
);

export default httpClient;
