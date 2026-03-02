<template>
  <div class="flex min-h-screen items-center justify-center bg-slate-50 px-4 py-12 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold tracking-tight text-slate-900">
          AI Content Platform
        </h2>
        <p class="mt-2 text-center text-sm text-slate-600">
          Unlock your creative potential with AI
        </p>
      </div>

      <div class="bg-white px-8 py-10 shadow-sm border border-slate-200 rounded-xl">
        <RouterView />
      </div>
    </div>
  </div>
</template>
