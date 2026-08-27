<script setup>
import { ref } from 'vue';

/** Саарал дэвсгэртэй задардаг мөр (Figma загварын ТХГ дэлгэрэнгүй хэсэг). */
const props = defineProps({
    title: { type: String, required: true },
    open: { type: Boolean, default: false },
});

const isOpen = ref(props.open);
</script>

<template>
    <div class="overflow-hidden rounded-lg bg-stone-100">
        <button
            class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left text-[15px] font-semibold text-stone-800 transition hover:bg-stone-200/60"
            @click="isOpen = !isOpen"
        >
            {{ title }}
            <svg
                class="h-4 w-4 shrink-0 text-stone-500 transition-transform"
                :class="{ 'rotate-180': isOpen }"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"
            ><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
        </button>
        <div v-if="isOpen" class="px-5 pb-5 text-sm leading-relaxed text-stone-600">
            <slot />
        </div>
    </div>
</template>
