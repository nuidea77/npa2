<script setup>
defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
});
</script>

<template>
    <section class="relative overflow-hidden bg-pine-700 text-white">
        <!-- Дэвсгэрийн line-art эмблем -->
        <svg class="pointer-events-none absolute -right-10 -top-8 h-64 w-auto text-white/10" viewBox="0 0 120 84" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
            <path d="M60 15v8M42 20l5 6M78 20l-5 6M30 33l8 3M90 33l-8 3M48 44a12 12 0 0 1 24 0" />
            <path d="M10 62c8-12 18-18 30-18 8 0 12 3 17 8M52 60c6-9 13-14 22-14 12 0 20 6 28 16" />
            <path d="M22 56l4-7 4 7M20 61l6-9 6 9M36 58l3.5-6 3.5 6M46 62c5 4 5 8-2 12M58 61c8 5 9 10 0 15" />
        </svg>
        <div class="relative mx-auto max-w-7xl px-4 py-14 md:py-20">
            <h1 class="max-w-4xl text-2xl font-extrabold leading-snug md:text-4xl">{{ title }}</h1>
            <p v-if="subtitle" class="mt-4 max-w-3xl leading-relaxed text-white/80 md:text-lg">{{ subtitle }}</p>
            <slot />
        </div>
    </section>
</template>
