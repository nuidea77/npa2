<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from '../bootstrap';
import { locale, t } from '../i18n';
import { programs } from '../content/programs';
import { useAuthStore } from '../stores/auth';
import NewsCard from '../components/NewsCard.vue';
import PhotoCarousel from '../components/PhotoCarousel.vue';
import FeedbackInline from '../components/FeedbackInline.vue';
import Icon from '../components/Icon.vue';

const router = useRouter();
const auth = useAuthStore();
const data = ref(null);
const allParks = ref([]);

const aimags = computed(() => auth.settings?.aimags ?? []);

const heroSlides = computed(() => {
    const featured = data.value?.parks ?? [];
    if (featured.length) return featured.map((p) => ({ image: p.image, title: p.name, id: p.id }));
    return [{ image: '/images/yosemite.jpg', title: '', id: null }];
});

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
        <!-- Hero — зургийн carousel (Figma) -->
        <section class="relative">
            <PhotoCarousel :slides="heroSlides" height-class="h-[380px] md:h-[520px] rounded-none" :auto="true">
                <template #default="{ slide }">
                    <router-link
                        v-if="slide?.id"
                        :to="`/parks/${slide.id}`"
                        class="absolute bottom-10 left-4 flex items-center gap-2 rounded-lg border border-white/70 bg-black/20 px-4 py-2.5 text-sm font-bold text-white backdrop-blur-sm transition hover:bg-white hover:text-pine-800 md:left-10"
                    >
                        {{ t('home.featuredPark') }} <Icon name="arrow-right" :size="16" />
                    </router-link>
                </template>
            </PhotoCarousel>
        </section>

        <!-- ТХГ хайх карт (Figma — hero доор давхарласан ногоон карт) -->
        <section class="relative z-10 mx-auto -mt-10 max-w-3xl px-4">
            <div class="rounded-xl bg-pine-700 p-5 shadow-xl md:p-6">
                <div class="grid items-end gap-3 md:grid-cols-[1fr_auto_1fr]">
                    <div>
                        <label class="mb-1.5 block text-sm font-bold text-white">{{ t('home.searchTitle') }}</label>
                        <select v-model="searchAimag" class="input w-full" @change="goAimag">
                            <option value="" disabled>{{ t('home.searchByAimag') }}</option>
                            <option v-for="a in aimags" :key="a" :value="a">{{ a }}</option>
                        </select>
                    </div>
                    <div class="hidden pb-2.5 text-sm text-white/70 md:block">{{ t('common.or') }}</div>
                    <div>
                        <label class="mb-1.5 block text-sm font-bold text-white">{{ t('home.searchByName') }}</label>
                        <select v-model="searchPark" class="input w-full" @change="goPark">
                            <option value="" disabled>{{ t('home.searchByName') }}</option>
                            <option v-for="p in allParks" :key="p.id" :value="p.id">{{ p.name }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <!-- ХӨТӨЛБӨРҮҮД -->
        <section class="mx-auto max-w-7xl px-4 pb-4 pt-14">
            <div class="flex items-center justify-between">
                <h2 class="section-title">{{ t('home.programsTitle') }}</h2>
                <router-link to="/programs" class="view-all">
                    {{ t('common.viewAll') }}
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="m9 6 6 6-6 6"/></svg>
                </router-link>
            </div>
            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
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
        </section>

        <!-- МЭДЭЭ, МЭДЭЭЛЭЛ -->
        <section class="mx-auto max-w-7xl px-4 py-14">
            <div class="flex items-center justify-between">
                <h2 class="section-title">{{ t('home.newsSection') }}</h2>
                <router-link to="/news" class="view-all">
                    {{ t('common.viewAll') }}
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="m9 6 6 6-6 6"/></svg>
                </router-link>
            </div>
            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <NewsCard v-for="n in (data?.news ?? []).slice(0, 4)" :key="n.id" :item="n" />
            </div>
        </section>

        <!-- Санал хүсэлт илгээх -->
        <FeedbackInline />
    </div>
</template>

<style scoped>
@reference '../../css/app.css';

.section-title {
    @apply text-xl font-extrabold uppercase tracking-wide text-stone-900 md:text-2xl;
}
.view-all {
    @apply flex items-center gap-1 text-[13px] font-bold uppercase tracking-wide text-stone-500 transition hover:text-pine-700;
}
.input {
    @apply rounded-lg border-0 bg-white px-3.5 py-2.5 text-sm text-stone-700 outline-none;
}
</style>
