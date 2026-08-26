<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import axios from '../bootstrap';
import { locale, t } from '../i18n';

const route = useRoute();
const news = ref(null);
const loading = ref(true);

const title = computed(() => (locale.value === 'en' && news.value?.title_en ? news.value.title_en : news.value?.title));
const body = computed(() => (locale.value === 'en' && news.value?.body_en ? news.value.body_en : news.value?.body));

onMounted(async () => {
    try {
        const { data } = await axios.get(`/api/news/${route.params.id}`);
        news.value = data.news;
        // Гадаад линктэй мэдээ бол шууд тухайн линк рүү үсэрнэ
        if (news.value?.type === 'external' && news.value.external_url) {
            window.location.href = news.value.external_url;
        }
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <div class="mx-auto max-w-3xl px-4 py-12">
        <router-link to="/news" class="text-sm font-medium text-pine-600 hover:text-pine-800">← {{ t('common.back') }}</router-link>

        <div v-if="loading" class="mt-6 text-stone-400">{{ t('common.loading') }}</div>

        <article v-else-if="news">
            <div class="mt-4 text-sm text-stone-400">{{ news.published_at }}</div>
            <h1 class="mt-2 text-2xl font-bold leading-snug text-pine-900 md:text-3xl">{{ title }}</h1>
            <img v-if="news.image" :src="news.image" :alt="title" class="mt-6 w-full rounded-2xl shadow" />
            <div class="prose-mn mt-6 whitespace-pre-line leading-relaxed text-stone-700">{{ body }}</div>
        </article>
    </div>
</template>
