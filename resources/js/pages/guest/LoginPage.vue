<script setup lang="ts">
import { useMutation } from '@tanstack/vue-query';
import { useRouter } from 'vue-router';
import { useForm } from '@/composables/useForm';
import { useAuthStore } from '@/stores/auth.store';
import type { ILoginPayload } from '@/types';

const router = useRouter();
const authStore = useAuthStore();

// 1. Setup Form with your composable
// Added 'remember' to match standard Laravel session auth
const { form, errors, submit, clearError } = useForm<ILoginPayload & { remember: boolean }>({
  email: '',
  password: '',
  remember: false,
});

// 2. Define the Login Mutation
const loginMutation = useMutation({
  mutationFn: (credentials) => authStore.login(credentials),
  onSuccess: () => {
    router.push({ name: 'Dashboard' });
  },
});

// 3. Form Submission Handler
const handleLogin = async () => {
  try {
    await submit(loginMutation);
  } catch (err) {
    // Errors are automatically populated in 'errors' ref via useForm
  }
};
</script>

<template>
  <div class="space-y-8">
    <div class="text-center">
      <h1 class="text-2xl font-bold tracking-tight text-slate-900">Welcome back</h1>
      <p class="text-sm text-slate-500 mt-2">Please enter your details to sign in</p>
    </div>

    <form @submit.prevent="handleLogin" class="space-y-5">
      <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="transform -translate-y-2 opacity-0"
        enter-to-class="transform translate-y-0 opacity-100"
      >
        <div v-if="errors.general" class="rounded-lg bg-red-50 p-4 border border-red-100">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div class="ml-3">
              <p class="text-sm font-medium text-red-800">{{ errors.general }}</p>
            </div>
          </div>
        </div>
      </Transition>

      <div class="space-y-1.5">
        <label for="email" class="block text-sm font-semibold text-slate-700">Email Address</label>
        <div class="relative group">
          <input
            v-model="form.email"
            type="email"
            id="email"
            @input="clearError('email')"
            placeholder="name@company.com"
            class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-900 transition-all duration-200 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 sm:text-sm outline-none"
            :class="{ '!border-red-500 !ring-red-500/10': errors.email }"
            required
          />
        </div>
        <p v-if="errors.email" class="text-xs font-medium text-red-500 mt-1">{{ errors.email }}</p>
      </div>

      <div class="space-y-1.5">
        <div class="flex items-center justify-between">
          <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
        </div>
        <div class="relative group">
          <input
            v-model="form.password"
            type="password"
            id="password"
            @input="clearError('password')"
            placeholder="••••••••"
            class="block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-slate-900 transition-all duration-200 focus:bg-white focus:border-indigo-600 focus:ring-4 focus:ring-indigo-500/10 sm:text-sm outline-none"
            :class="{ '!border-red-500 !ring-red-500/10': errors.password }"
            required
          />
        </div>
        <p v-if="errors.password" class="text-xs font-medium text-red-500 mt-1">
          {{ errors.password }}
        </p>
      </div>

      <div class="flex items-center">
        <input
          v-model="form.remember"
          id="remember"
          type="checkbox"
          class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-600 cursor-pointer"
        />
        <label for="remember" class="ml-2 block text-sm text-slate-600 cursor-pointer select-none">
          Keep me signed in
        </label>
      </div>

      <button
        type="submit"
        :disabled="loginMutation.isPending.value"
        class="group relative flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition-all duration-200 hover:bg-slate-800 active:scale-[0.98] disabled:opacity-70 disabled:active:scale-100 cursor-pointer shadow-lg shadow-slate-200"
      >
        <svg
          v-if="loginMutation.isPending.value"
          class="animate-spin h-4 w-4 text-white"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
        >
          <circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
          ></circle>
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          ></path>
        </svg>
        <span>{{ loginMutation.isPending.value ? 'Verifying Account...' : 'Sign In' }}</span>
      </button>
    </form>
  </div>
</template>
