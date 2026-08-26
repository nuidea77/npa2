<script setup>
/**
 * NPA-ийн албан ёсны лого (nationalparkacademy.org-оос).
 *  - white: цагаан лого + бичвэр — ногоон дэвсгэрт (header, footer)
 *  - badge: ногоон дөрвөлжин дээрх эмблем — цайвар дэвсгэрт (нэвтрэх г.м)
 */
defineProps({
    variant: { type: String, default: 'white' }, // white | badge
    imgClass: { type: String, default: 'h-10 w-auto' },
});
</script>

<template>
    <img
        v-if="variant === 'badge'"
        src="/images/favicon.png"
        alt="National Park Academy"
        class="rounded-2xl"
        :class="imgClass"
    />
    <img
        v-else
        src="/images/logo-white.png"
        alt="National Park Academy"
        :class="imgClass"
    />
</template>
