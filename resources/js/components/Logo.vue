<script setup>
/**
 * NPA line-art лого — нар, уулс, ой, гол бүхий контур эмблем.
 * Өнгө нь currentColor тул цагаан/ногоон аль ч дэвсгэрт ажиллана.
 */
defineProps({
    showText: { type: Boolean, default: true },
    emblemClass: { type: String, default: 'h-11 w-auto' },
    textClass: { type: String, default: 'text-[13px]' },
});
</script>

<template>
    <span class="inline-flex items-center gap-2.5">
        <svg :class="emblemClass" viewBox="0 0 120 84" fill="none" stroke="currentColor" stroke-width="3.2"
             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <!-- Хүрээ -->
            <rect x="4" y="4" width="112" height="76" rx="16" />
            <!-- Нарны туяа -->
            <path d="M60 15v8" />
            <path d="M42 20l5 6" />
            <path d="M78 20l-5 6" />
            <path d="M30 33l8 3" />
            <path d="M90 33l-8 3" />
            <!-- Нар (хагас) -->
            <path d="M48 44a12 12 0 0 1 24 0" />
            <!-- Уулс, толгод -->
            <path d="M10 62c8-12 18-18 30-18 8 0 12 3 17 8" />
            <path d="M52 60c6-9 13-14 22-14 12 0 20 6 28 16" />
            <!-- Моднууд -->
            <path d="M22 56l4-7 4 7" />
            <path d="M20 61l6-9 6 9" />
            <path d="M36 58l3.5-6 3.5 6" />
            <!-- Гол -->
            <path d="M46 62c5 4 5 8-2 12" />
            <path d="M58 61c8 5 9 10 0 15" />
            <!-- Шувууд -->
            <path d="M88 24c2-2.5 4-2.5 6 0" />
            <path d="M97 30c1.7-2 3.3-2 5 0" />
        </svg>
        <span v-if="showText" class="font-extrabold uppercase leading-[1.1] tracking-wide" :class="textClass">
            National<br />Park<br />Academy
        </span>
    </span>
</template>
