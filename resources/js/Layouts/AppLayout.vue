<template>
  <div class="min-h-screen flex flex-col">
    <header class="bg-white shadow">
        <div class="flex flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:py-8">
            <div>
                <ApplicationLogo class="h-20 w-20 fill-current text-gray-500" />
            </div>
        </div>
    </header>
    <main class="flex-grow">
      <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<style scoped>
.max-w-7xl {
  max-width: 1368px;
}
</style>
