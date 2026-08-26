<script setup>
import { onMounted, ref } from 'vue';
import axios from '../bootstrap';
import { t } from '../i18n';
import PageHero from '../components/PageHero.vue';

const parks = ref([]);
const aimags = ref([]);
const types = ref([]);
const loading = ref(true);
const filters = ref({ aimag: '', type: '', q: '' });

async function load() {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/parks', { params: filters.value });
        parks.value = data.parks;
        aimags.value = data.aimags;
        types.value = data.types;
    } finally {
        loading.value = false;
    }
}

function clearFilters() {
    filters.value = { aimag: '', type: '', q: '' };
    load();
}

onMounted(load);
</script>

<template>
    <div>
        <PageHero
            :title="t('nav.parks')"
            subtitle="Энэ хэсэгт нийт Монгол Улсын тусгай хамгаалалттай газруудыг хайж, мэдээлэл авах боломжтой."
        />

        <div class="mx-auto max-w-7xl px-4 py-10">
            <!-- Хайлт -->
            <form class="grid gap-3 rounded-2xl border border-pine-100 bg-pine-50/60 p-5 md:grid-cols-5" @submit.prevent="load">
                <select v-model="filters.aimag" class="input">
                    <option value="">Аймгаар ({{ t('common.all') }})</option>
                    <option v-for="a in aimags" :key="a" :value="a">{{ a }}</option>
                </select>
                <select v-model="filters.type" class="input">
                    <option value="">Төрлөөр ({{ t('common.all') }})</option>
                    <option v-for="ty in types" :key="ty" :value="ty">{{ ty }}</option>
                </select>
                <input v-model="filters.q" class="input md:col-span-2" placeholder="Тусгай хамгаалалттай газрын нэрээр хайх..." />
                <div class="flex gap-2">
                    <button class="flex-1 rounded-xl bg-pine-700 px-4 py-2.5 font-semibold text-white transition hover:bg-pine-800">{{ t('common.search') }}</button>
                    <button type="button" class="rounded-xl border border-stone-200 bg-white px-3 py-2.5 text-sm text-stone-500 hover:bg-stone-50" @click="clearFilters">✕</button>
                </div>
            </form>

            <!-- Үр дүн -->
            <div v-if="loading" class="mt-8 text-stone-400">{{ t('common.loading') }}</div>
            <p v-else-if="!parks.length" class="mt-8 rounded-2xl bg-stone-100 p-6 text-stone-500">{{ t('common.empty') }}</p>

            <div v-else class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <router-link
                    v-for="p in parks"
                    :key="p.id"
                    :to="`/parks/${p.id}`"
                    class="group overflow-hidden rounded-2xl border border-stone-100 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >
                    <img :src="p.image || '/images/park-mountain.svg'" :alt="p.name" class="h-40 w-full object-cover" />
                    <div class="p-4">
                        <div class="flex items-center gap-2 text-xs">
                            <span class="rounded-full bg-pine-100 px-2.5 py-0.5 font-medium text-pine-800">{{ p.type }}</span>
                            <span class="text-stone-400">{{ p.aimag }}</span>
                        </div>
                        <h3 class="mt-2 font-bold text-stone-800 group-hover:text-pine-700">{{ p.name }}</h3>
                        <p class="mt-1 line-clamp-2 text-sm text-stone-500">{{ p.intro }}</p>
                        <div class="mt-2 text-xs text-stone-400">{{ p.org?.name }}</div>
                    </div>
                </router-link>
            </div>
        </div>
    </div>
</template>

<style scoped>
@reference '../../css/app.css';

.input {
    @apply rounded-xl border border-stone-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-pine-400;
}
</style>
