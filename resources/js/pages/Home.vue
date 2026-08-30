<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from '../bootstrap';
import { locale, t } from '../i18n';
import { programs } from '../content/programs';
import { useAuthStore } from '../stores/auth';
import NewsCard from '../components/NewsCard.vue';
import FeedbackInline from '../components/FeedbackInline.vue';
import Icon from '../components/Icon.vue';

const router = useRouter();
const auth = useAuthStore();
const data = ref(null);
const allParks = ref([]);

const aimags = computed(() => auth.settings?.aimags ?? []);

const searchAimag = ref('');
const searchPark = ref('');

function goAimag() {
    if (searchAimag.value) router.push({ path: '/parks', query: { aimag: searchAimag.value } });
}
function goPark() {
    if (searchPark.value) router.push(`/parks/${searchPark.value}`);
}

onMounted(async () => {
    try {
        const [home, parks] = await Promise.all([axios.get('/api/home'), axios.get('/api/parks')]);
        data.value = home.data;
        allParks.value = parks.data.parks;
    } catch { /* холболтгүй үед хоосон */ }
});
</script>

<template>
    <div>
        <!-- Hero — nationalparkacademy.org загвар: бүтэн дэлгэцийн зураг, төвдөө гарчиг -->
        <section class="relative flex min-h-[88vh] items-center justify-center overflow-hidden">
            <div class="absolute inset-0 z-0">
                <img src="/images/hero.jpg" alt="National Park landscape" class="h-full w-full object-cover" />
                <div class="absolute inset-0 bg-gradient-to-b from-pine-700/70 via-pine-700/50 to-[#fcfcfc]"></div>
            </div>
            <div class="relative z-10 mx-auto max-w-7xl px-4 pt-16 text-center sm:px-6 lg:px-8">
                <div class="mx-auto max-w-4xl">
                    <h1 class="mb-6 text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                        {{ t('home.heroTitle') }}
                    </h1>
                    <p class="mx-auto mb-10 max-w-3xl text-base leading-relaxed text-white/90 sm:text-lg lg:text-xl">
                        {{ t('home.heroSubtitle') }}
                    </p>
                    <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <router-link
                            to="/programs"
                            class="group inline-flex items-center justify-center rounded-lg bg-white px-8 py-3 text-base font-medium text-pine-700 transition-all hover:bg-white/90"
                        >
                            {{ t('home.viewPrograms') }}
                            <Icon name="arrow-right" :size="18" class="ml-2 transition-transform group-hover:translate-x-1" />
                        </router-link>
                        <router-link
                            to="/about"
                            class="inline-flex items-center justify-center rounded-lg border-2 border-white/30 bg-white/10 px-8 py-3 text-base font-medium text-white backdrop-blur-sm transition-all hover:bg-white/20"
                        >
                            {{ t('nav.about') }}
                        </router-link>
                    </div>
                </div>
            </div>
            <!-- Гүйлгэх заалт -->
            <div class="absolute bottom-8 left-1/2 z-10 -translate-x-1/2">
                <div class="flex h-10 w-6 justify-center rounded-full border-2 border-black/30 p-2">
                    <div class="h-3 w-1 animate-bounce rounded-full bg-black/40"></div>
                </div>
            </div>
        </section>

        <!-- ТХГ хайх зурвас -->
        <section class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="rounded-xl bg-pine-700 p-6 shadow-lg">
                    <div class="grid items-end gap-6 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-white">{{ t('home.searchTitle') }}</label>
                            <select v-model="searchAimag" class="hero-select" @change="goAimag">
                                <option value="" disabled>{{ t('home.searchByAimag') }}</option>
                                <option v-for="a in aimags" :key="a" :value="a">{{ a }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-white">{{ t('home.searchByName') }}</label>
                            <select v-model="searchPark" class="hero-select" @change="goPark">
                                <option value="" disabled>{{ t('home.searchByName') }}</option>
                                <option v-for="p in allParks" :key="p.id" :value="p.id">{{ p.name }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ХӨТӨЛБӨРҮҮД -->
        <section class="py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-12 text-center">
                    <h2 class="mb-4 text-3xl font-bold text-stone-900 sm:text-4xl">{{ t('home.programsTitle') }}</h2>
                    <p class="mx-auto max-w-2xl text-lg text-stone-500">{{ t('home.programsSub') }}</p>
                </div>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <router-link
                        v-for="p in programs.slice(0, 4)"
                        :key="p.slug"
                        :to="p.path"
                        class="group relative block h-80 overflow-hidden rounded-xl bg-pine-900"
                    >
                        <img :src="p.image" :alt="t(p.key)" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                        <div class="absolute inset-0 bg-gradient-to-t from-pine-950/95 via-pine-900/35 to-transparent"></div>
                        <div class="absolute inset-x-0 bottom-0 p-4">
                            <h3 class="text-lg font-bold leading-snug text-white">{{ t(p.key) }}</h3>
                            <p class="mt-1.5 line-clamp-2 text-[13px] leading-relaxed text-white/70">{{ p.short[locale] || p.short.mn }}</p>
                            <span class="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-white/90 transition group-hover:gap-2.5">
                                {{ t('common.detail') }} <Icon name="arrow-right" :size="15" />
                            </span>
                        </div>
                    </router-link>
                </div>
                <div class="mt-12 text-center">
                    <router-link to="/programs" class="see-more group">
                        {{ t('common.viewAll') }}
                        <Icon name="arrow-right" :size="18" class="ml-2 transition-transform group-hover:translate-x-1" />
                    </router-link>
                </div>
            </div>
        </section>

        <!-- МЭДЭЭ -->
        <section class="bg-[#f5f7f4]/60 py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-12 text-center">
                    <h2 class="mb-4 text-3xl font-bold text-stone-900 sm:text-4xl">{{ t('home.newsSection') }}</h2>
                    <p class="mx-auto max-w-2xl text-lg text-stone-500">{{ t('home.newsSub') }}</p>
                </div>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <NewsCard v-for="n in (data?.news ?? []).slice(0, 4)" :key="n.id" :item="n" />
                </div>
                <div class="mt-12 text-center">
                    <router-link to="/news" class="see-more group">
                        {{ t('common.viewAll') }}
                        <Icon name="arrow-right" :size="18" class="ml-2 transition-transform group-hover:translate-x-1" />
                    </router-link>
                </div>
            </div>
        </section>

        <!-- Санал хүсэлт илгээх -->
        <FeedbackInline />
    </div>
</template>

<style scoped>
@reference '../../css/app.css';

.hero-select {
    @apply w-full rounded-lg border-2 border-white/20 bg-white/10 px-4 py-3 text-white outline-none transition-colors focus:border-white/50;
}
.hero-select option {
    @apply text-stone-800;
}
.see-more {
    @apply inline-flex items-center justify-center rounded-lg bg-pine-700 px-8 py-3 text-base font-medium text-white transition-all hover:bg-pine-700/90;
}
</style>
