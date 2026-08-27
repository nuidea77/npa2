<script setup>
import { onMounted, ref } from 'vue';
import axios from '../bootstrap';
import { locale, t } from '../i18n';
import NewsCard from '../components/NewsCard.vue';
import FeedbackInline from '../components/FeedbackInline.vue';

const items = ref([]);
const page = ref(1);
const lastPage = ref(1);
const loading = ref(true);

async function load(p = 1) {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/news', { params: { page: p } });
        items.value = data.items;
        page.value = data.page;
        lastPage.value = data.last_page;
        window.scrollTo({ top: 0 });
    } finally {
        loading.value = false;
    }
}

onMounted(() => load());
</script>

<template>
    <div>
        <div class="mx-auto max-w-7xl px-4 pt-10">
            <h1 class="text-2xl font-extrabold uppercase tracking-wide text-stone-900 md:text-[26px]">
                {{ locale === 'en' ? 'News & updates' : 'Мэдээ, мэдээлэл' }}
            </h1>

            <div v-if="loading" class="py-16 text-stone-400">{{ t('common.loading') }}</div>
            <p v-else-if="!items.length" class="my-10 rounded-xl bg-stone-100 p-6 text-stone-500">{{ t('common.empty') }}</p>

            <div v-else class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <NewsCard v-for="n in items" :key="n.id" :item="n" />
            </div>

            <!-- Хуудаслалт (Figma: < 1 2 3 >) -->
            <div v-if="lastPage > 1" class="mt-10 flex justify-end gap-1.5 pb-14">
                <button class="page" :disabled="page === 1" @click="load(page - 1)">‹</button>
                <button
                    v-for="p in lastPage"
                    :key="p"
                    class="page"
                    :class="{ active: p === page }"
                    @click="load(p)"
                >{{ p }}</button>
                <button class="page" :disabled="page === lastPage" @click="load(page + 1)">›</button>
            </div>
            <div v-else class="pb-14"></div>
        </div>

        <FeedbackInline />
    </div>
</template>

<style scoped>
@reference '../../css/app.css';

.page {
    @apply flex h-9 w-9 items-center justify-center rounded-md bg-stone-100 text-sm font-semibold text-stone-600 transition hover:bg-stone-200 disabled:opacity-40;
}
.page.active {
    @apply bg-pine-700 text-white;
}
</style>
