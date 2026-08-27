<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from '../bootstrap';
import { t } from '../i18n';
import DetailHeader from '../components/DetailHeader.vue';
import PhotoCarousel from '../components/PhotoCarousel.vue';
import Accordion from '../components/Accordion.vue';
import FeedbackInline from '../components/FeedbackInline.vue';
import Icon from '../components/Icon.vue';

const route = useRoute();
const park = ref(null);
const siblings = ref([]);
const loading = ref(true);

const slides = computed(() => park.value ? [{ image: park.value.image || '/images/park-mountain.svg', title: park.value.name }] : []);

/** Figma: задардаг хэсгүүд — эхнийх нь нээлттэй */
const sections = computed(() => park.value ? [
    { title: 'ХЗ-ны товч танилцуулга', text: park.value.intro || park.value.org?.intro },
    { title: 'Онцолж буй байгалийн тогтоц газар', text: park.value.highlights },
    { title: 'Амьтад', text: park.value.animals },
    { title: 'Газар зүйн онцлог', text: park.value.geography },
    { title: 'Нутгийн зон олон', text: park.value.locals },
    { title: 'Хэрхэн хүрч очих вэ?', text: park.value.get_there },
    { title: 'Хэрхэн аялах вэ?', text: park.value.travel },
    { title: 'Аялал жуулчлалын үйлчилгээ', text: park.value.services },
    { title: 'Анхааруулга, уриалга', text: park.value.warnings },
    { title: 'Хамгаалалтын захиргаа', text: park.value.admin_info },
].filter((s) => s.text) : []);

const mapUrl = computed(() => {
    if (!park.value?.lat || !park.value?.lng) return null;
    return `https://www.google.com/maps?q=${park.value.lat},${park.value.lng}`;
});

async function load() {
    loading.value = true;
    try {
        const { data } = await axios.get(`/api/parks/${route.params.id}`);
        park.value = data.park;
        siblings.value = data.siblings;
        window.scrollTo({ top: 0 });
    } finally {
        loading.value = false;
    }
}

onMounted(load);
watch(() => route.params.id, load);
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

            <div class="mx-auto max-w-5xl px-4 pb-14">
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

                <!-- Задардаг хэсгүүд -->
                <div class="mt-8 grid gap-3">
                    <Accordion v-for="(s, i) in sections" :key="s.title" :title="s.title" :open="i === 0">
                        <p class="whitespace-pre-line">{{ s.text }}</p>
                    </Accordion>

                    <Accordion v-if="siblings.length" title="Тухайн ХЗ-нд харьяалагдах бусад ТХГ">
                        <div class="grid gap-2">
                            <router-link
                                v-for="s in siblings"
                                :key="s.id"
                                :to="`/parks/${s.id}`"
                                class="flex items-center gap-2 rounded-lg bg-white px-3.5 py-2.5 text-sm font-medium text-stone-700 transition hover:text-pine-700"
                            >
                                <Icon name="mountain" :size="16" class="text-pine-600" /> {{ s.name }}
                                <span class="text-xs text-stone-400">({{ s.aimag }})</span>
                            </router-link>
                        </div>
                    </Accordion>

                    <Accordion v-if="mapUrl" :title="t('common.viewOnMap')">
                        <a
                            :href="mapUrl"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex items-center gap-2 rounded-lg bg-pine-700 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-pine-800"
                        >
                            <Icon name="map" :size="17" /> Google Maps дээр нээх
                        </a>
                    </Accordion>
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
</style>
