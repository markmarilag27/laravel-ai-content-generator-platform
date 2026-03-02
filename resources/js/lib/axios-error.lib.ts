import { AxiosError } from 'axios'
import type { IErrorResponse } from '@/types'

/**
 * Type guard to safely check if an error is an AxiosError.
 * @param error The unknown error value.
 * @returns True if the error is an AxiosError.
 */
export function isAxiosError<T = unknown>(error: unknown): error is AxiosError<T> {
  return typeof error === 'object' && error !== null && 'isAxiosError' in error
}

/**
 * Extracts the structured error response from an AxiosError or returns a fallback.
 * @template T Must extend your base IErrorResponse structure.
 * @param error The unknown error object.
 * @param fallback The message to use if the error is not an AxiosError or data is missing.
 * @returns An object of type T (IErrorResponse or a refinement of it).
 */
export function extractAxiosError<T extends IErrorResponse = IErrorResponse>(
  error: unknown,
  fallback: string = 'Unexpected error occurred'
): T {
  if (isAxiosError<T>(error)) {
    // Check if response.data exists and is an object
    if (
      error.response?.data &&
      typeof error.response.data === 'object' &&
      error.response.data !== null
    ) {
      // We still use 'as T' here, trusting the API contract matches the T generic.
      return error.response.data as T
    }

    // Fallback for Axios error without response data (e.g., timeout, network issue)
    return { message: 'Network or Unknown Axios error occurred' } as T
  }

  // Ensure the returned object contains only the fields required by IErrorResponse
  return { message: fallback } as T
}
