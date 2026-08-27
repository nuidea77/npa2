<script setup>
import { t } from '../i18n';
import Icon from './Icon.vue';
import ShareButtons from './ShareButtons.vue';

/**
 * Дэлгэрэнгүй хуудасны толгой (Figma): зүүнд Буцах, голд гарчиг,
 * доор нь огноо + share товчнууд.
 */
defineProps({
    title: { type: String, required: true },
    back: { type: String, default: '/' },
    dateLabel: { type: String, default: '' },
    date: { type: String, default: '' },
});
</script>

<template>
    <div class="mx-auto max-w-5xl px-4 pt-8">
        <div class="relative flex items-center justify-center py-1">
            <router-link
                :to="back"
                class="absolute left-0 flex items-center gap-1.5 text-sm font-medium text-stone-500 transition hover:text-pine-700"
            >
                <Icon name="arrow-right" :size="15" class="rotate-180" /> {{ t('common.back') }}
            </router-link>
            <h1 class="max-w-[70%] text-center text-xl font-extrabold text-pine-800 md:text-2xl">{{ title }}</h1>
        </div>
        <div class="mt-4 flex items-center justify-between">
            <div class="text-sm text-stone-500">
                <template v-if="date">{{ dateLabel || t('detail.published') }}: {{ date }}</template>
            </div>
            <ShareButtons />
        </div>
    </div>
</template>
