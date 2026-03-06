<script setup lang="ts">
import AppFooter from '@/components/AppFooter.vue';
import AppHeader from '@/components/AppHeader.vue';
</script>

<template>
  <div class="layout-wrapper">
    <AppHeader class="app-header" />

    <main class="main-content dark:bg-slate-950">
      <div class="content-container">
        <RouterView />
      </div>
    </main>

    <AppFooter class="app-footer" />
  </div>
</template>

<style scoped>
/* 1. Configuration: Define your heights here */
.layout-wrapper {
  --h-header: 64px;
  --h-footer: 80px; /* Adjust this to match your AppFooter height */

  display: flex;
  flex-direction: column;
  background-color: #f8fafc; /* slate-50 */
}

/* 2. Header & Footer: Set explicit heights */
.app-header {
  height: var(--h-header);
}

.app-footer {
  height: var(--h-footer);
}

/* 3. Main: The calculation logic */
.main-content {
  /* Subtract header and footer from 100dvh (Dynamic Viewport Height).
     This ensures the footer is pushed to the bottom on short pages.
  */
  min-height: calc(100dvh - var(--h-header) - var(--h-footer));
  width: 100%;
}

/* 4. Padding & Max Width */
.content-container {
  max-width: 80rem; /* max-w-7xl */
  margin: 0 auto;
  padding: 2rem 1rem; /* py-8 px-4 */
}

@media (min-width: 640px) {
  .content-container {
    padding: 2rem 1.5rem; /* sm:px-6 */
  }
}

/* Ensure nav links in header match the 64px height */
:deep(nav a) {
  height: var(--h-header);
  display: flex;
  align-items: center;
}
</style>
