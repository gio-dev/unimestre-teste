<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:py-8"
    >
        <div>
            <ApplicationLogo class="h-20 w-20 fill-current text-gray-500" />
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md md:max-w-xl sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
