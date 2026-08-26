<script setup>
import { onMounted, ref } from 'vue';
import axios from '../bootstrap';
import { locale, t } from '../i18n';
import { programs } from '../content/programs';
import NewsCard from '../components/NewsCard.vue';
import RegButton from '../components/RegButton.vue';

const data = ref(null);

onMounted(async () => {
    try {
        const res = await axios.get('/api/home');
        data.value = res.data;
    } catch { /* холболтгүй үед хоосон */ }
});
</script>

<template>
    <div>
        <!-- Hero -->
        <section class="relative overflow-hidden bg-pine-900 text-white">
            <img src="/images/hero.svg" alt="" class="absolute inset-0 h-full w-full object-cover opacity-90" />
            <div class="absolute inset-0 bg-gradient-to-t from-pine-950/90 via-pine-900/40 to-transparent"></div>
            <div class="relative mx-auto flex max-w-7xl flex-col items-start px-4 py-24 md:py-36">
                <div class="rounded-full bg-white/10 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest backdrop-blur">
                    Монгол Экологи Төв · 2012 {{ t('home.statsSince') }}
                </div>
                <h1 class="mt-5 max-w-3xl text-4xl font-extrabold leading-tight drop-shadow md:text-6xl">
                    {{ t('home.heroTitle') }}
                </h1>
                <p class="mt-4 max-w-2xl text-lg text-pine-50 drop-shadow md:text-xl">
                    {{ t('home.heroSubtitle') }}
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <router-link
                        to="/parks"
                        class="rounded-full bg-sand-400 px-6 py-3 font-bold text-pine-950 shadow-lg transition hover:bg-sand-300"
                    >{{ t('home.exploreParks') }}</router-link>
                    <router-link
                        to="/programs"
                        class="rounded-full border-2 border-white/70 px-6 py-3 font-bold text-white backdrop-blur transition hover:bg-white/10"
                    >{{ t('home.viewPrograms') }}</router-link>
                </div>
            </div>
        </section>

        <!-- Тоон үзүүлэлт -->
        <section class="border-b border-pine-100 bg-white">
            <div class="mx-auto grid max-w-7xl grid-cols-2 gap-6 px-4 py-10 text-center md:grid-cols-4">
                <div>
                    <div class="text-3xl font-extrabold text-pine-700">2012</div>
                    <div class="mt-1 text-sm text-stone-500">{{ t('home.statsSince') }}</div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-pine-700">{{ data?.stats?.children ?? '360+' }}</div>
                    <div class="mt-1 text-sm text-stone-500">{{ t('home.statsChildren') }}</div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-pine-700">{{ data?.stats?.orgs ?? '—' }}</div>
                    <div class="mt-1 text-sm text-stone-500">{{ t('home.statsOrgs') }}</div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-pine-700">{{ data?.stats?.parks ?? '—' }}</div>
                    <div class="mt-1 text-sm text-stone-500">{{ t('home.statsParks') }}</div>
                </div>
            </div>
        </section>

        <!-- Хөтөлбөрүүд -->
        <section class="mx-auto max-w-7xl px-4 py-14">
            <h2 class="section-title">{{ t('home.programsTitle') }}</h2>
            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <router-link
                    v-for="p in programs"
                    :key="p.slug"
                    :to="p.path"
                    class="group rounded-2xl border border-stone-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-pine-200 hover:shadow-md"
                >
                    <div class="text-3xl">{{ p.icon }}</div>
                    <h3 class="mt-3 font-bold leading-snug text-stone-800 group-hover:text-pine-700">{{ t(p.key) }}</h3>
                    <p class="mt-2 text-sm text-stone-500">{{ p.short[locale] || p.short.mn }}</p>
                </router-link>
            </div>
        </section>

        <!-- Удахгүй болох арга хэмжээ -->
        <section v-if="data?.events?.length" class="bg-pine-50/60">
            <div class="mx-auto max-w-7xl px-4 py-14">
                <h2 class="section-title">{{ t('home.eventsTitle') }}</h2>
                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    <div v-for="e in data.events" :key="e.id" class="rounded-2xl border border-pine-100 bg-white p-5 shadow-sm">
                        <div class="text-xs font-semibold uppercase tracking-wide text-pine-500">{{ e.year }}</div>
                        <h3 class="mt-1 font-bold leading-snug text-stone-800">{{ e.title }}</h3>
                        <p class="mt-2 line-clamp-2 text-sm text-stone-500">{{ e.description }}</p>
                        <div class="mt-4">
                            <RegButton :event="e" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ТХГ хайлт -->
        <section class="relative overflow-hidden bg-pine-800 text-white">
            <div class="mx-auto grid max-w-7xl items-center gap-8 px-4 py-14 md:grid-cols-2">
                <div>
                    <h2 class="text-2xl font-bold md:text-3xl">{{ t('home.parksTitle') }}</h2>
                    <p class="mt-3 text-pine-100">{{ t('home.parksText') }}</p>
                    <router-link
                        to="/parks"
                        class="mt-6 inline-block rounded-full bg-white px-6 py-3 font-bold text-pine-800 transition hover:bg-pine-50"
                    >{{ t('home.exploreParks') }} →</router-link>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <router-link
                        v-for="p in (data?.parks ?? [])"
                        :key="p.id"
                        :to="`/parks/${p.id}`"
                        class="group overflow-hidden rounded-2xl bg-white/10 backdrop-blur transition hover:bg-white/20"
                    >
                        <img :src="p.image" :alt="p.name" class="h-24 w-full object-cover" />
                        <div class="p-3">
                            <div class="text-sm font-semibold leading-snug">{{ p.name }}</div>
                            <div class="mt-0.5 text-xs text-pine-200">{{ p.aimag }}</div>
                        </div>
                    </router-link>
                </div>
            </div>
        </section>

        <!-- Мэдээ -->
        <section class="mx-auto max-w-7xl px-4 py-14">
            <div class="flex items-center justify-between">
                <h2 class="section-title">{{ t('home.newsTitle') }}</h2>
                <router-link to="/news" class="text-sm font-semibold text-pine-600 hover:text-pine-800">{{ t('home.allNews') }} →</router-link>
            </div>
            <div class="mt-8 grid gap-5 md:grid-cols-3">
                <NewsCard v-for="n in (data?.news ?? [])" :key="n.id" :item="n" />
            </div>
        </section>
    </div>
</template>

<style scoped>
@reference '../../css/app.css';

.section-title {
    @apply text-2xl font-bold text-pine-900 md:text-3xl;
}
</style>
