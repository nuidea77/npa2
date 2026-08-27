<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import axios from '../bootstrap';
import { locale, t } from '../i18n';
import DetailHeader from '../components/DetailHeader.vue';
import FeedbackInline from '../components/FeedbackInline.vue';

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
    <div>
        <div v-if="loading" class="mx-auto max-w-5xl px-4 py-16 text-stone-400">{{ t('common.loading') }}</div>

        <template v-else-if="news">
            <DetailHeader
                :title="title"
                back="/news"
                :date="news.published_at?.replaceAll('-', '/')"
            />

            <article class="mx-auto max-w-5xl px-4 pb-14">
                <img v-if="news.image" :src="news.image" :alt="title" class="mt-6 h-72 w-full rounded-xl object-cover md:h-[420px]" />
                <div class="prose-mn mx-auto mt-7 max-w-3xl whitespace-pre-line leading-relaxed text-stone-600">{{ body }}</div>
            </article>

            <FeedbackInline />
        </template>
    </div>
</template>
