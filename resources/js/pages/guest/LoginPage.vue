<script setup lang="ts">
import { useMutation } from '@tanstack/vue-query';
import { useRouter } from 'vue-router';
import { Sparkles, Mail, Lock, AlertCircle, Loader2 } from 'lucide-vue-next';
import { useForm } from '@/composables/useForm';
import { useAuthStore } from '@/stores/auth.store';
import type { ILoginPayload } from '@/types';

const router = useRouter();
const authStore = useAuthStore();

const { form, errors, submit, clearError } = useForm<ILoginPayload>({
  email: '',
  password: '',
  remember: false,
});

const loginMutation = useMutation({
  mutationFn: (credentials: ILoginPayload) => authStore.login(credentials),
  onSuccess: () => {
    router.push({ name: 'BrandVoiceProfile' });
  },
});

const handleLogin = async () => {
  await submit(loginMutation);
};
</script>

<template>
  <div class="w-full max-w-md space-y-8 px-4">
    <div class="flex flex-col items-center text-center">
      <div
        class="flex size-12 items-center justify-center rounded-xl bg-primary/10 text-primary mb-6"
      >
        <Sparkles :size="32" fill="currentColor" class="fill-primary/20" />
      </div>
      <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
        Welcome back
      </h1>
      <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
        Please enter your details to sign in
      </p>
    </div>

    <form @submit.prevent="handleLogin" class="mt-8 space-y-6">
      <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="transform -translate-y-2 opacity-0"
        enter-to-class="transform translate-y-0 opacity-100"
      >
        <div
          v-if="errors.general"
          class="flex items-center gap-3 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-900/50 dark:bg-red-900/20"
        >
          <AlertCircle class="size-5 text-red-500" />
          <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ errors.general }}</p>
        </div>
      </Transition>

      <div class="space-y-4">
        <div class="space-y-1.5">
          <label for="email" class="text-sm font-semibold text-slate-700 dark:text-slate-300"
            >Email Address</label
          >
          <div class="relative group">
            <div
              class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 group-focus-within:text-primary transition-colors"
            >
              <Mail :size="18" />
            </div>
            <input
              v-model="form.email"
              type="email"
              id="email"
              @input="clearError('email')"
              placeholder="name@company.com"
              class="flex h-12 w-full rounded-xl border border-slate-300 bg-white pl-11 pr-4 text-sm text-slate-900 transition-all placeholder:text-slate-400 focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-500 outline-none"
              :class="{ '!border-red-500 !ring-red-500': errors.email }"
              required
            />
          </div>
          <p v-if="errors.email" class="text-xs font-medium text-red-500">{{ errors.email }}</p>
        </div>

        <div class="space-y-1.5">
          <div class="flex items-center justify-between">
            <label for="password" class="text-sm font-semibold text-slate-700 dark:text-slate-300"
              >Password</label
            >
          </div>
          <div class="relative group">
            <div
              class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 group-focus-within:text-primary transition-colors"
            >
              <Lock :size="18" />
            </div>
            <input
              v-model="form.password"
              type="password"
              id="password"
              @input="clearError('password')"
              placeholder="••••••••"
              class="flex h-12 w-full rounded-xl border border-slate-300 bg-white pl-11 pr-4 text-sm text-slate-900 transition-all placeholder:text-slate-400 focus:border-primary focus:ring-1 focus:ring-primary dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-500 outline-none"
              :class="{ '!border-red-500 !ring-red-500': errors.password }"
              required
            />
          </div>
          <p v-if="errors.password" class="text-xs font-medium text-red-500">
            {{ errors.password }}
          </p>
        </div>
      </div>

      <div class="flex items-center">
        <input
          v-model="form.remember"
          id="remember"
          type="checkbox"
          class="size-4 rounded border-slate-300 text-primary focus:ring-primary dark:border-slate-700 dark:bg-slate-900 cursor-pointer transition-colors"
        />
        <label
          for="remember"
          class="ml-2 block text-sm font-medium text-slate-600 dark:text-slate-400 cursor-pointer select-none hover:text-slate-900 dark:hover:text-slate-200"
        >
          Keep me signed in
        </label>
      </div>

      <button
        type="submit"
        :disabled="loginMutation.isPending.value"
        class="flex cursor-pointer w-full items-center justify-center gap-2 rounded-xl bg-primary px-4 py-3.5 text-sm font-semibold text-white transition-all hover:bg-primary/90 active:scale-[0.98] disabled:opacity-70 disabled:active:scale-100 shadow-sm shadow-primary/20"
      >
        <Loader2 v-if="loginMutation.isPending.value" class="animate-spin size-5" />
        <span>{{ loginMutation.isPending.value ? 'Verifying Account...' : 'Sign In' }}</span>
      </button>

      <p class="text-center text-sm text-slate-600 dark:text-slate-400">
        Don't have an account?
        <RouterLink to="/register" class="font-semibold text-primary hover:underline"
          >Create an account</RouterLink
        >
      </p>
    </form>
  </div>
</template>
