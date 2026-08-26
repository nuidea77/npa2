<script setup>
import { onMounted, ref } from 'vue';
import axios from '../bootstrap';
import { t } from '../i18n';
import PageHero from '../components/PageHero.vue';
import NewsCard from '../components/NewsCard.vue';

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
        <PageHero :title="t('nav.news')" />

        <div class="mx-auto max-w-7xl px-4 py-12">
            <div v-if="loading" class="text-stone-400">{{ t('common.loading') }}</div>
            <p v-else-if="!items.length" class="rounded-2xl bg-stone-100 p-6 text-stone-500">{{ t('common.empty') }}</p>

            <div v-else class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <NewsCard v-for="n in items" :key="n.id" :item="n" />
            </div>

            <div v-if="lastPage > 1" class="mt-10 flex justify-center gap-2">
                <button
                    v-for="p in lastPage"
                    :key="p"
                    class="h-9 w-9 rounded-full text-sm font-semibold transition"
                    :class="p === page ? 'bg-pine-700 text-white' : 'bg-white text-stone-600 hover:bg-pine-50'"
                    @click="load(p)"
                >{{ p }}</button>
            </div>
        </div>
    </div>
</template>
