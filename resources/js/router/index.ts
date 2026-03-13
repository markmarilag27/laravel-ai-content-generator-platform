import {
  createRouter,
  createWebHistory,
  RouteRecordRaw,
  RouteLocationNormalized,
} from 'vue-router';
import { useAuthStore } from '@/stores/auth.store';

const routes: RouteRecordRaw[] = [
  {
    path: '/login',
    component: () => import('@/layouts/GuestLayout.vue'),
    meta: { requiresGuest: true },
    children: [
      {
        path: '',
        name: 'Login',
        component: () => import('@/pages/guest/LoginPage.vue'),
      },
    ],
  },

  {
    path: '/',
    component: () => import('@/layouts/AuthLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'BrandVoiceProfile',
        component: () => import('@/pages/auth/BrandVoiceProfilePage.vue'),
      },
      {
        path: 'brand-voices/create',
        name: 'BrandVoiceProfileCreate',
        component: () => import('@/pages/auth/BrandVoiceProfileCreatePage.vue'),
      },
      {
        path: 'brand-voices/:id/edit',
        name: 'BrandVoiceProfileEdit',
        component: () => import('@/pages/auth/BrandVoiceProfileEditPage.vue'),
      },
      {
        path: 'contents',
        name: 'Contents',
        component: () => import('@/pages/auth/ContentPage.vue'),
      },
      {
        path: 'contents/create',
        name: 'ContentsCreate',
        component: () => import('@/pages/auth/ContentCreatePage.vue'),
      },
      {
        path: 'campaigns',
        name: 'Campaigns',
        component: () => import('@/pages/auth/CampaignPage.vue'),
      },
      {
        path: 'campaigns/create',
        name: 'CampaignCreate',
        component: () => import('@/pages/auth/CampaignCreatePage.vue'),
      },
      {
        path: 'campaigns/:id/show',
        name: 'CampaignDetail',
        component: () => import('@/pages/auth/CampaignDetailPage.vue'),
      },
    ],
  },

  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/pages/error/NotFoundPage.vue'),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to: RouteLocationNormalized) => {
  const authStore = useAuthStore();

  if (!authStore.initialized) {
    await authStore.fetchUser();
  }

  const needsAuth = to.matched.some((record) => record.meta.requiresAuth);
  const needsGuest = to.matched.some((record) => record.meta.requiresGuest);

  if (needsAuth && !authStore.isAuthenticated) {
    return { name: 'Login' };
  }

  if (needsGuest && authStore.isAuthenticated) {
    return { name: 'Dashboard' };
  }
});

export default router;
