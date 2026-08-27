<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from '../bootstrap';
import { locale, t } from '../i18n';
import Icon from '../components/Icon.vue';
import FeedbackInline from '../components/FeedbackInline.vue';

const route = useRoute();
const parks = ref([]);
const aimags = ref([]);
const loading = ref(true);
const filters = ref({ aimag: String(route.query.aimag ?? ''), q: '', park_id: '' });

const title = computed(() => (locale.value === 'en' ? 'Protected areas' : 'Тусгай хамгаалалттай газрууд'));

async function load() {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/parks', { params: { aimag: filters.value.aimag, q: filters.value.q } });
        parks.value = data.parks;
        aimags.value = data.aimags;
    } finally {
        loading.value = false;
    }
}

const router = useRouter();

function goPark() {
    if (filters.value.park_id) router.push(`/parks/${filters.value.park_id}`);
}

onMounted(load);
</script>

<template>
    <div>
        <div class="mx-auto max-w-7xl px-4 pt-10">
            <!-- Гарчиг + шүүлтүүр (Figma) -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h1 class="text-2xl font-extrabold uppercase tracking-wide text-stone-900 md:text-[26px]">{{ title }}</h1>
                <div class="flex flex-wrap gap-2.5">
                    <select v-model="filters.aimag" class="select" @change="load">
                        <option value="">{{ t('home.searchByAimag') }}</option>
                        <option v-for="a in aimags" :key="a" :value="a">{{ a }}</option>
                    </select>
                    <select v-model="filters.park_id" class="select" @change="goPark">
                        <option value="">{{ t('home.searchByName') }}</option>
                        <option v-for="p in parks" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                </div>
            </div>

            <!-- Илэрцийн тоо + хайлт -->
            <div class="mt-6 grid items-center gap-4 md:grid-cols-[1fr_2fr_1fr]">
                <div class="text-sm text-stone-500">
                    {{ parks.length }} {{ t('parks.results') }}
                </div>
                <div class="relative">
                    <input
                        v-model="filters.q"
                        class="w-full rounded-lg border border-stone-300 py-2.5 pl-4 pr-11 text-sm outline-none transition placeholder:text-stone-400 focus:border-pine-500"
                        :placeholder="t('parks.searchPh')"
                        @keyup.enter="load"
                    />
                    <button class="absolute right-1.5 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-md text-stone-400 transition hover:text-pine-700" @click="load">
                        <Icon name="search" :size="18" />
                    </button>
                </div>
                <div></div>
            </div>

            <!-- Картууд: зураг + ногоон блок (Figma) -->
            <div v-if="loading" class="py-16 text-stone-400">{{ t('common.loading') }}</div>
            <p v-else-if="!parks.length" class="my-10 rounded-xl bg-stone-100 p-6 text-stone-500">{{ t('common.empty') }}</p>

            <div v-else class="mt-8 grid gap-6 pb-14 md:grid-cols-2">
                <router-link
                    v-for="p in parks"
                    :key="p.id"
                    :to="`/parks/${p.id}`"
                    class="group overflow-hidden rounded-xl"
                >
                    <div class="h-56 overflow-hidden md:h-64">
                        <img :src="p.image || '/images/park-mountain.svg'" :alt="p.name" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                    </div>
                    <div class="bg-pine-700 p-5 text-white">
                        <h3 class="text-lg font-bold">{{ p.name }}</h3>
                        <p class="mt-1.5 line-clamp-2 text-sm leading-relaxed text-white/75">{{ p.intro }}</p>
                        <span class="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-white/90 transition group-hover:gap-2.5">
                            {{ t('common.detail') }} <Icon name="arrow-right" :size="15" />
                        </span>
                    </div>
                </router-link>
            </div>
        </div>

        <FeedbackInline />
    </div>
</template>

<style scoped>
@reference '../../css/app.css';

.select {
    @apply rounded-lg border border-stone-300 bg-white px-3.5 py-2.5 text-sm text-stone-600 outline-none transition focus:border-pine-500;
}
</style>
