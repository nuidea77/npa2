<script setup>
import { computed } from 'vue';
import { locale, t } from '../i18n';
import Icon from './Icon.vue';

/** Мэдээний фото карт — доод хэсэгтээ гүн ногоон градиент дээр гарчигтай (Figma). */
const props = defineProps({
    item: { type: Object, required: true },
});

const title = computed(() => (locale.value === 'en' && props.item.title_en ? props.item.title_en : props.item.title));

const host = computed(() => {
    try { return new URL(props.item.external_url).hostname.replace('www.', ''); } catch { return ''; }
});
</script>

<template>
    <component
        :is="item.type === 'external' ? 'a' : 'router-link'"
        v-bind="item.type === 'external'
            ? { href: item.external_url, target: '_blank', rel: 'noopener' }
            : { to: `/news/${item.id}` }"
        class="group relative block h-72 overflow-hidden rounded-xl bg-pine-900"
    >
        <img
            :src="item.image || '/images/news-external.svg'"
            :alt="title"
            class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-pine-950/95 via-pine-900/40 to-black/10"></div>

        <!-- Гадаад эх сурвалжийн тэмдэг -->
        <div
            v-if="item.type === 'external' && host"
            class="absolute left-4 top-1/2 flex items-center gap-1.5 rounded-full bg-black/30 px-2.5 py-1 text-xs font-medium text-white backdrop-blur-sm"
        >
            <Icon name="globe" :size="13" /> {{ host }}
        </div>

        <div class="absolute inset-x-0 bottom-0 p-4">
            <div class="text-[11px] font-medium text-white/60">{{ item.published_at }}</div>
            <h3 class="mt-1 line-clamp-3 text-[15px] font-bold leading-snug text-white">{{ title }}</h3>
            <span class="mt-2.5 inline-flex items-center gap-1.5 text-sm font-semibold text-white/90 transition group-hover:gap-2.5">
                {{ t('common.detail') }} <Icon name="arrow-right" :size="15" />
            </span>
        </div>
    </component>
</template>
