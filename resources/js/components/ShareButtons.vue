<script setup>
import { computed } from 'vue';

const url = computed(() => encodeURIComponent(typeof window !== 'undefined' ? window.location.href : ''));
</script>

<template>
    <div class="flex gap-2">
        <a
            :href="`https://www.facebook.com/sharer/sharer.php?u=${url}`"
            target="_blank" rel="noopener" title="Facebook"
            class="flex h-8 w-8 items-center justify-center rounded-full bg-stone-200 text-stone-600 transition hover:bg-pine-700 hover:text-white"
        >
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.23.2 2.23.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0 0 22 12z"/></svg>
        </a>
        <a
            :href="`https://twitter.com/intent/tweet?url=${url}`"
            target="_blank" rel="noopener" title="X / Twitter"
            class="flex h-8 w-8 items-center justify-center rounded-full bg-stone-200 text-stone-600 transition hover:bg-pine-700 hover:text-white"
        >
            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.9 1.15h3.68l-8.04 9.19L24 22.85h-7.41l-5.8-7.58-6.64 7.58H.47l8.6-9.83L0 1.15h7.59l5.24 6.93zm-1.29 19.5h2.04L6.49 3.24H4.3z"/></svg>
        </a>
    </div>
</template>
