<script setup>
defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
});
</script>

<template>
    <section class="bg-gradient-to-br from-pine-800 via-pine-700 to-pine-600 text-white">
        <div class="mx-auto max-w-7xl px-4 py-12 md:py-16">
            <h1 class="max-w-4xl text-2xl font-bold leading-snug md:text-4xl">{{ title }}</h1>
            <p v-if="subtitle" class="mt-3 max-w-3xl text-pine-100 md:text-lg">{{ subtitle }}</p>
            <slot />
        </div>
    </section>
</template>
