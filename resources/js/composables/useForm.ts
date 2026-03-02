import { ref, Ref } from 'vue';
import type { IErrorResponse } from '@/types';
import { extractAxiosError } from '@/lib/axios-error.lib';
import { UseMutationReturnType } from '@tanstack/vue-query';

export function useForm<T extends Record<string, any>>(initial: T) {
  const initialState = { ...initial };
  const form = ref({ ...initial }) as Ref<T>;
  const serverErrors = ref<Record<string, string>>({});

  const clearError = (field: keyof T | string) => {
    delete serverErrors.value[field as string];
  };

  const reset = () => {
    form.value = { ...initialState };
    serverErrors.value = {};
  };

  const submit = async <TResponse, TError>(
    mutation: UseMutationReturnType<TResponse, TError, T, unknown>
  ) => {
    serverErrors.value = {};

    try {
      return await mutation.mutateAsync(form.value);
    } catch (err) {
      const error = extractAxiosError<IErrorResponse>(err);

      if (error.errors) {
        serverErrors.value = Object.fromEntries(
          Object.entries(error.errors).map(([k, v]) => [k, v[0]])
        );
      } else {
        serverErrors.value.general = error.message;
      }

      throw err;
    }
  };

  return {
    form,
    errors: serverErrors,
    reset,
    clearError,
    submit,
  };
}
