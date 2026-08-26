<script setup>
import { computed, onMounted, ref } from 'vue';
import axios from '../bootstrap';
import { locale, t } from '../i18n';
import { programs } from '../content/programs';
import NewsCard from '../components/NewsCard.vue';
import RegButton from '../components/RegButton.vue';
import Icon from '../components/Icon.vue';

const data = ref(null);

const purpose = computed(() => [
    {
        icon: 'tree',
        title: locale.value === 'en' ? 'Environmental Stewardship' : 'Байгаль хамгаалал',
        text: locale.value === 'en'
            ? "Promoting sustainable practices and conservation of Mongolia's unique natural heritage."
            : 'Монгол орны өвөрмөц байгалийн өвийг хамгаалж, тогтвортой менежментийг түгээн дэлгэрүүлнэ.',
    },
    {
        icon: 'users',
        title: locale.value === 'en' ? 'Community Engagement' : 'Орон нутгийн түншлэл',
        text: locale.value === 'en'
            ? 'Building partnerships between local communities and protected areas for sustainable conservation.'
            : 'Нутгийн иргэд, нөхөрлөл, түнш байгууллагуудтай хамтран бүсийн эдийн засагт хувь нэмэр оруулна.',
    },
    {
        icon: 'book',
        title: locale.value === 'en' ? 'Academic Excellence' : 'Боловсрол, сургалт',
        text: locale.value === 'en'
            ? 'Providing world-class education and training in conservation and protected area management.'
            : 'Байгаль хамгаалагч, мэргэжилтнүүдийг олон улсын түвшний сургалтаар чадавхжуулна.',
    },
]);

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
        <section class="relative overflow-hidden bg-pine-700 text-white">
            <svg class="pointer-events-none absolute -right-16 top-1/2 hidden h-[420px] w-auto -translate-y-1/2 text-white/[0.13] md:block" viewBox="0 0 120 84" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                <rect x="4" y="4" width="112" height="76" rx="16" />
                <path d="M60 15v8M42 20l5 6M78 20l-5 6M30 33l8 3M90 33l-8 3M48 44a12 12 0 0 1 24 0" />
                <path d="M10 62c8-12 18-18 30-18 8 0 12 3 17 8M52 60c6-9 13-14 22-14 12 0 20 6 28 16" />
                <path d="M22 56l4-7 4 7M20 61l6-9 6 9M36 58l3.5-6 3.5 6M46 62c5 4 5 8-2 12M58 61c8 5 9 10 0 15" />
                <path d="M88 24c2-2.5 4-2.5 6 0M97 30c1.7-2 3.3-2 5 0" />
            </svg>
            <div class="relative mx-auto max-w-7xl px-4 py-20 md:py-28">
                <div class="inline-block rounded-lg border border-white/25 px-3.5 py-1.5 text-xs font-bold uppercase tracking-widest text-white/80">
                    Монгол Экологи Төв · 2012 {{ t('home.statsSince') }}
                </div>
                <h1 class="mt-6 max-w-3xl text-4xl font-extrabold leading-tight md:text-[56px] md:leading-[1.1]">
                    {{ t('home.heroTitle') }}
                </h1>
                <p class="mt-5 max-w-2xl text-lg leading-relaxed text-white/85 md:text-xl">
                    {{ t('home.heroSubtitle') }}
                </p>
                <div class="mt-9 flex flex-wrap gap-3">
                    <router-link
                        to="/parks"
                        class="rounded-xl bg-white px-6 py-3.5 font-bold text-pine-800 transition hover:bg-sand-100"
                    >{{ t('home.exploreParks') }}</router-link>
                    <router-link
                        to="/programs"
                        class="rounded-xl border-2 border-white/60 px-6 py-3.5 font-bold text-white transition hover:bg-white/10"
                    >{{ t('home.viewPrograms') }}</router-link>
                </div>
            </div>
        </section>

        <!-- Тоон үзүүлэлт -->
        <section class="border-b border-stone-200 bg-white">
            <div class="mx-auto grid max-w-7xl grid-cols-2 gap-6 px-4 py-12 text-center md:grid-cols-4">
                <div>
                    <div class="text-4xl font-extrabold text-pine-800">2012</div>
                    <div class="mt-1.5 text-sm text-stone-500">{{ t('home.statsSince') }}</div>
                </div>
                <div>
                    <div class="text-4xl font-extrabold text-pine-800">{{ data?.stats?.children ?? '360+' }}</div>
                    <div class="mt-1.5 text-sm text-stone-500">{{ t('home.statsChildren') }}</div>
                </div>
                <div>
                    <div class="text-4xl font-extrabold text-pine-800">{{ data?.stats?.orgs ?? '—' }}</div>
                    <div class="mt-1.5 text-sm text-stone-500">{{ t('home.statsOrgs') }}</div>
                </div>
                <div>
                    <div class="text-4xl font-extrabold text-pine-800">{{ data?.stats?.parks ?? '—' }}</div>
                    <div class="mt-1.5 text-sm text-stone-500">{{ t('home.statsParks') }}</div>
                </div>
            </div>
        </section>

        <!-- Бидний зорилго -->
        <section class="mx-auto max-w-4xl px-4 py-20">
            <h2 class="text-center text-3xl font-extrabold text-pine-900 md:text-4xl">
                {{ locale === 'en' ? 'Our Purpose' : 'Бидний зорилго' }}
            </h2>
            <p class="mx-auto mt-5 max-w-2xl text-center text-lg leading-relaxed text-stone-500">
                {{ locale === 'en'
                    ? "Dedicated to building capacity and fostering conservation excellence across Mongolia's protected areas"
                    : 'Монгол Улсын Тусгай хамгаалалттай газруудын чадавхыг бэхжүүлж, байгаль хамгааллын шилдэг туршлагыг төлөвшүүлнэ' }}
            </p>
            <div class="mt-14 grid gap-10">
                <div v-for="p in purpose" :key="p.title" class="flex items-start gap-5">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-pine-100 text-pine-800">
                        <Icon :name="p.icon" :size="28" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-pine-900 md:text-2xl">{{ p.title }}</h3>
                        <p class="mt-2 leading-relaxed text-stone-500">{{ p.text }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Хөтөлбөрүүд -->
        <section class="bg-white">
            <div class="mx-auto max-w-7xl px-4 py-20">
                <h2 class="section-title">{{ t('home.programsTitle') }}</h2>
                <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <router-link
                        v-for="p in programs"
                        :key="p.slug"
                        :to="p.path"
                        class="group rounded-2xl border border-stone-200 bg-stone-50 p-6 transition hover:-translate-y-0.5 hover:border-pine-300 hover:bg-white hover:shadow-md"
                    >
                        <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-pine-100 text-pine-800">
                            <Icon :name="p.icon" :size="26" />
                        </div>
                        <h3 class="mt-4 font-bold leading-snug text-pine-900 group-hover:text-pine-700">{{ t(p.key) }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-stone-500">{{ p.short[locale] || p.short.mn }}</p>
                    </router-link>
                </div>
            </div>
        </section>

        <!-- Удахгүй болох арга хэмжээ -->
        <section v-if="data?.events?.length" class="mx-auto max-w-7xl px-4 py-20">
            <h2 class="section-title">{{ t('home.eventsTitle') }}</h2>
            <div class="mt-10 grid gap-5 md:grid-cols-3">
                <div v-for="e in data.events" :key="e.id" class="rounded-2xl border border-stone-200 bg-white p-6">
                    <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-pine-600">
                        <Icon name="calendar" :size="15" /> {{ e.year }}
                    </div>
                    <h3 class="mt-2.5 font-bold leading-snug text-pine-900">{{ e.title }}</h3>
                    <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-stone-500">{{ e.description }}</p>
                    <div class="mt-5">
                        <RegButton :event="e" />
                    </div>
                </div>
            </div>
        </section>

        <!-- ТХГ хайлт -->
        <section class="bg-pine-700 text-white">
            <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-20 md:grid-cols-2">
                <div>
                    <h2 class="text-3xl font-extrabold md:text-4xl">{{ t('home.parksTitle') }}</h2>
                    <p class="mt-4 text-lg leading-relaxed text-white/80">{{ t('home.parksText') }}</p>
                    <router-link
                        to="/parks"
                        class="mt-8 inline-block rounded-xl bg-white px-6 py-3.5 font-bold text-pine-800 transition hover:bg-sand-100"
                    >{{ t('home.exploreParks') }} →</router-link>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <router-link
                        v-for="p in (data?.parks ?? [])"
                        :key="p.id"
                        :to="`/parks/${p.id}`"
                        class="group overflow-hidden rounded-2xl border border-white/15 bg-white/5 transition hover:bg-white/15"
                    >
                        <img :src="p.image" :alt="p.name" class="h-24 w-full object-cover" />
                        <div class="p-3.5">
                            <div class="text-sm font-bold leading-snug">{{ p.name }}</div>
                            <div class="mt-1 text-xs text-white/60">{{ p.aimag }}</div>
                        </div>
                    </router-link>
                </div>
            </div>
        </section>

        <!-- Мэдээ -->
        <section class="mx-auto max-w-7xl px-4 py-20">
            <div class="flex items-center justify-between">
                <h2 class="section-title">{{ t('home.newsTitle') }}</h2>
                <router-link to="/news" class="text-sm font-bold text-pine-700 hover:text-pine-900">{{ t('home.allNews') }} →</router-link>
            </div>
            <div class="mt-10 grid gap-6 md:grid-cols-3">
                <NewsCard v-for="n in (data?.news ?? [])" :key="n.id" :item="n" />
            </div>
        </section>
    </div>
</template>

<style scoped>
@reference '../../css/app.css';

.section-title {
    @apply text-3xl font-extrabold text-pine-900 md:text-4xl;
}
</style>
