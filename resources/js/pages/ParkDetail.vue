<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from '../bootstrap';
import { t } from '../i18n';
import DetailHeader from '../components/DetailHeader.vue';
import PhotoCarousel from '../components/PhotoCarousel.vue';
import FeedbackInline from '../components/FeedbackInline.vue';
import Icon from '../components/Icon.vue';

const route = useRoute();
const park = ref(null);
const siblings = ref([]);
const loading = ref(true);
const active = ref('');

const slides = computed(() => park.value ? [{ image: park.value.image || '/images/park-mountain.svg', title: park.value.name }] : []);

const mapUrl = computed(() => {
    if (!park.value?.lat || !park.value?.lng) return null;
    return `https://www.google.com/maps?q=${park.value.lat},${park.value.lng}`;
});
const mapEmbed = computed(() => {
    if (!park.value?.lat || !park.value?.lng) return null;
    return `https://maps.google.com/maps?q=${park.value.lat},${park.value.lng}&z=9&hl=mn&output=embed`;
});

/** Зүүн талын таб жагсаалт — текстэн хэсгүүд + харьяа ТХГ + газрын зураг */
const tabs = computed(() => {
    if (!park.value) return [];
    const p = park.value;
    const list = [
        { key: 'intro', title: 'ХЗ-ны товч танилцуулга', icon: 'book-open', text: p.intro || p.org?.intro },
        { key: 'highlights', title: 'Онцолж буй байгалийн тогтоц газар', icon: 'mountain', text: p.highlights },
        { key: 'animals', title: 'Амьтад', icon: 'binoculars', text: p.animals },
        { key: 'geography', title: 'Газар зүйн онцлог', icon: 'map', text: p.geography },
        { key: 'locals', title: 'Нутгийн зон олон', icon: 'home', text: p.locals },
        { key: 'get_there', title: 'Хэрхэн хүрч очих вэ?', icon: 'car', text: p.get_there },
        { key: 'travel', title: 'Хэрхэн аялах вэ?', icon: 'compass', text: p.travel },
        { key: 'services', title: 'Аялал жуулчлалын үйлчилгээ', icon: 'tent', text: p.services },
        { key: 'warnings', title: 'Анхааруулга, уриалга', icon: 'warning', text: p.warnings },
        { key: 'admin_info', title: 'Хамгаалалтын захиргаа', icon: 'building', text: p.admin_info },
    ].filter((s) => s.text);

    if (siblings.value.length) {
        list.push({ key: 'siblings', title: 'Харьяалагдах бусад ТХГ', icon: 'map-pin', type: 'siblings' });
    }
    if (mapEmbed.value) {
        list.push({ key: 'map', title: t('common.viewOnMap'), icon: 'map', type: 'map' });
    }
    return list;
});

const activeTab = computed(() => tabs.value.find((s) => s.key === active.value) ?? tabs.value[0]);

async function load() {
    loading.value = true;
    try {
        const { data } = await axios.get(`/api/parks/${route.params.id}`);
        park.value = data.park;
        siblings.value = data.siblings;
        active.value = '';
        window.scrollTo({ top: 0 });
    } finally {
        loading.value = false;
    }
}

onMounted(load);
watch(() => route.params.id, load);
watch(tabs, (v) => { if (v.length && !v.some((s) => s.key === active.value)) active.value = v[0].key; });
</script>

<template>
    <div>
        <div v-if="loading" class="mx-auto max-w-5xl px-4 py-16 text-stone-400">{{ t('common.loading') }}</div>

        <div v-else-if="park">
            <DetailHeader
                :title="park.name"
                back="/parks"
                :date="park.updated_at?.slice(0, 10).replaceAll('-', '/')"
            />

            <div class="mx-auto max-w-6xl px-4 pb-14">
                <!-- Зургийн carousel -->
                <PhotoCarousel :slides="slides" height-class="h-72 md:h-[420px]" class="mt-5" />

                <!-- Мэдээллийн тор (Figma: 3 багана, хуваагч зураастай) -->
                <div class="mt-8 grid gap-x-8 gap-y-6 border-b border-stone-200 pb-8 sm:grid-cols-3">
                    <div class="cell">
                        <div class="cell-label">Харъяалагдах аймаг</div>
                        <div class="cell-value">{{ park.aimag || '—' }}</div>
                    </div>
                    <div class="cell sm:border-l sm:border-stone-200 sm:pl-8">
                        <div class="cell-label">Төрөл</div>
                        <div class="cell-value">{{ park.type || '—' }}</div>
                    </div>
                    <div class="cell sm:border-l sm:border-stone-200 sm:pl-8">
                        <div class="cell-label">ТХГ-ын нэр</div>
                        <div class="cell-value">{{ park.name }}</div>
                    </div>
                    <div class="cell">
                        <div class="cell-label">ХЗ-ны лого</div>
                        <img :src="park.org?.logo || '/images/favicon.png'" alt="" class="mt-1 h-10 w-10 rounded-lg object-cover" />
                    </div>
                    <div class="cell sm:border-l sm:border-stone-200 sm:pl-8">
                        <div class="cell-label">Хамгаалалтын захиргааны нэр</div>
                        <div class="cell-value">{{ park.org?.name || '—' }}</div>
                    </div>
                    <div class="cell sm:border-l sm:border-stone-200 sm:pl-8">
                        <div class="cell-label">ХЗ-ны утас, и-мэйл</div>
                        <div class="cell-value">
                            {{ park.org?.phone }}<span v-if="park.org?.email">, <a :href="'mailto:' + park.org.email" class="underline">{{ park.org.email }}</a></span>
                        </div>
                    </div>
                </div>

                <!-- Хажуугийн таб жагсаалт + агуулга -->
                <div class="mt-8 grid gap-6 lg:grid-cols-[300px_1fr]">
                    <!-- Зүүн: босоо таб (mobile: хэвтээ гүйдэг) -->
                    <aside class="self-start lg:sticky lg:top-24">
                        <nav class="flex gap-2 overflow-x-auto pb-2 lg:flex-col lg:gap-1.5 lg:overflow-visible lg:pb-0">
                            <button
                                v-for="s in tabs"
                                :key="s.key"
                                class="tab"
                                :class="{ active: activeTab?.key === s.key }"
                                @click="active = s.key"
                            >
                                <Icon :name="s.icon" :size="17" class="shrink-0" :class="activeTab?.key === s.key ? 'text-white' : 'text-pine-600'" />
                                <span class="whitespace-nowrap lg:whitespace-normal">{{ s.title }}</span>
                            </button>
                        </nav>
                    </aside>

                    <!-- Баруун: сонгосон хэсгийн агуулга -->
                    <Transition name="fade" mode="out-in">
                        <div :key="activeTab?.key" class="min-h-[320px] rounded-xl border border-stone-200 bg-white p-6 md:p-8">
                            <h2 class="flex items-center gap-3 text-lg font-extrabold text-pine-800">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-pine-100 text-pine-700">
                                    <Icon :name="activeTab?.icon" :size="18" />
                                </span>
                                {{ activeTab?.title }}
                            </h2>

                            <!-- Харьяа ТХГ-ууд -->
                            <div v-if="activeTab?.type === 'siblings'" class="mt-5 grid gap-2.5 sm:grid-cols-2">
                                <router-link
                                    v-for="s in siblings"
                                    :key="s.id"
                                    :to="`/parks/${s.id}`"
                                    class="group flex items-center gap-3 rounded-lg border border-stone-200 p-3 transition hover:border-pine-400 hover:bg-pine-50"
                                >
                                    <img :src="s.image || '/images/park-mountain.svg'" alt="" class="h-14 w-20 shrink-0 rounded-md object-cover" />
                                    <div>
                                        <div class="text-sm font-bold text-stone-800 group-hover:text-pine-700">{{ s.name }}</div>
                                        <div class="mt-0.5 text-xs text-stone-400">{{ s.aimag }}</div>
                                    </div>
                                </router-link>
                            </div>

                            <!-- Газрын зураг -->
                            <div v-else-if="activeTab?.type === 'map'" class="mt-5">
                                <iframe
                                    :src="mapEmbed"
                                    class="h-80 w-full rounded-lg border border-stone-200 md:h-[420px]"
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                    title="Газрын зураг"
                                ></iframe>
                                <a
                                    :href="mapUrl"
                                    target="_blank"
                                    rel="noopener"
                                    class="mt-4 inline-flex items-center gap-2 rounded-lg bg-pine-700 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-pine-800"
                                >
                                    <Icon name="external" :size="16" /> Google Maps дээр нээх
                                </a>
                            </div>

                            <!-- Текстэн агуулга -->
                            <p v-else class="mt-5 whitespace-pre-line text-[15px] leading-relaxed text-stone-600">{{ activeTab?.text }}</p>
                        </div>
                    </Transition>
                </div>
            </div>

            <FeedbackInline />
        </div>
    </div>
</template>

<style scoped>
@reference '../../css/app.css';

.cell-label { @apply text-sm font-bold text-stone-800; }
.cell-value { @apply mt-1 text-sm leading-relaxed text-stone-500; }

.tab {
    @apply flex shrink-0 items-center gap-2.5 rounded-lg bg-stone-100 px-4 py-3 text-left text-sm font-semibold text-stone-700 transition hover:bg-stone-200 lg:w-full;
}
.tab.active {
    @apply bg-pine-700 text-white hover:bg-pine-700;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
