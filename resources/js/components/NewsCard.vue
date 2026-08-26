<script setup>
import { locale, t } from '../i18n';

const props = defineProps({
    item: { type: Object, required: true },
});

const title = () => (locale.value === 'en' && props.item.title_en ? props.item.title_en : props.item.title);
</script>

<template>
    <!-- Гадаад линктэй мэдээ шууд тухайн линк рүү үсэрнэ -->
    <a
        v-if="item.type === 'external'"
        :href="item.external_url"
        target="_blank"
        rel="noopener"
        class="group block overflow-hidden rounded-2xl border border-stone-100 bg-white shadow-sm transition hover:shadow-md"
    >
        <img :src="item.image || '/images/news-external.svg'" :alt="title()" class="h-44 w-full object-cover" />
        <div class="p-4">
            <div class="text-xs text-stone-400">{{ item.published_at }} · ↗</div>
            <h3 class="mt-1 font-semibold leading-snug text-stone-800 group-hover:text-pine-700">{{ title() }}</h3>
        </div>
    </a>

    <router-link
        v-else
        :to="`/news/${item.id}`"
        class="group block overflow-hidden rounded-2xl border border-stone-100 bg-white shadow-sm transition hover:shadow-md"
    >
        <img :src="item.image || '/images/news-camp.svg'" :alt="title()" class="h-44 w-full object-cover" />
        <div class="p-4">
            <div class="text-xs text-stone-400">{{ item.published_at }}</div>
            <h3 class="mt-1 font-semibold leading-snug text-stone-800 group-hover:text-pine-700">{{ title() }}</h3>
            <p v-if="item.excerpt" class="mt-2 line-clamp-2 text-sm text-stone-500">{{ item.excerpt }}</p>
            <span class="mt-2 inline-block text-sm font-medium text-pine-600">{{ t('common.readMore') }} →</span>
        </div>
    </router-link>
</template>
