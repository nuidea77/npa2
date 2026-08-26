<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from '../bootstrap';
import { t } from '../i18n';
import Icon from '../components/Icon.vue';

const route = useRoute();
const park = ref(null);
const siblings = ref([]);
const loading = ref(true);

const sections = computed(() => park.value ? [
    { key: 'intro', title: 'Танилцуулга', icon: 'book-open', text: park.value.intro },
    { key: 'highlights', title: 'Онцолж буй байгалийн тогтоц газар', icon: 'mountain', text: park.value.highlights },
    { key: 'animals', title: 'Амьтад', icon: 'binoculars', text: park.value.animals },
    { key: 'geography', title: 'Газар зүйн онцлог', icon: 'map', text: park.value.geography },
    { key: 'locals', title: 'Нутгийн зон олон', icon: 'home', text: park.value.locals },
    { key: 'get_there', title: 'Хэрхэн хүрч очих вэ?', icon: 'car', text: park.value.get_there },
    { key: 'travel', title: 'Хэрхэн аялах вэ?', icon: 'compass', text: park.value.travel },
    { key: 'services', title: 'Аялал жуулчлалын үйлчилгээ', icon: 'tent', text: park.value.services },
    { key: 'warnings', title: 'Анхааруулга, уриалга', icon: 'warning', text: park.value.warnings },
    { key: 'admin_info', title: 'Хамгаалалтын захиргаа', icon: 'building', text: park.value.admin_info },
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
            <!-- Толгой зураг -->
            <section class="relative h-64 overflow-hidden bg-pine-900 md:h-80">
                <img :src="park.image || '/images/park-mountain.svg'" :alt="park.name" class="h-full w-full object-cover" />
                <div class="absolute inset-0 bg-gradient-to-t from-pine-950/80 to-transparent"></div>
                <div class="absolute inset-x-0 bottom-0 mx-auto max-w-5xl px-4 pb-6 text-white">
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="rounded-full bg-white/20 px-3 py-1 font-medium backdrop-blur">{{ park.type }}</span>
                        <span class="rounded-full bg-white/20 px-3 py-1 font-medium backdrop-blur">{{ park.aimag }}</span>
                    </div>
                    <h1 class="mt-2 text-2xl font-bold md:text-4xl">{{ park.name }}</h1>
                </div>
            </section>

            <div class="mx-auto max-w-5xl px-4 py-10">
                <router-link to="/parks" class="text-sm font-medium text-pine-600 hover:text-pine-800">← {{ t('common.back') }}</router-link>

                <div class="mt-6 grid gap-6 md:grid-cols-3">
                    <!-- Мэдээллийн хэсгүүд -->
                    <div class="space-y-4 md:col-span-2">
                        <section v-for="s in sections" :key="s.key" class="rounded-2xl border border-stone-100 bg-white p-5 shadow-sm">
                            <h2 class="flex items-center gap-2.5 font-bold text-pine-900">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-pine-100 text-pine-800"><Icon :name="s.icon" :size="17" /></span>{{ s.title }}
                            </h2>
                            <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-stone-600">{{ s.text }}</p>
                        </section>
                    </div>

                    <!-- Хажуугийн мэдээлэл -->
                    <aside class="space-y-4">
                        <div v-if="park.org" class="rounded-2xl border border-pine-200 bg-pine-50 p-5">
                            <h2 class="font-bold text-pine-900">Хамгаалалтын захиргаа</h2>
                            <div class="mt-3 space-y-2 text-sm text-stone-700">
                                <div class="font-medium">{{ park.org.name }}</div>
                                <div v-if="park.org.address" class="flex items-start gap-2"><Icon name="map-pin" :size="16" class="mt-0.5 shrink-0 text-pine-700" /> {{ park.org.address }}</div>
                                <div v-if="park.org.phone" class="flex items-center gap-2"><Icon name="phone" :size="16" class="shrink-0 text-pine-700" /> {{ park.org.phone }}</div>
                                <div v-if="park.org.email" class="flex items-center gap-2"><Icon name="mail" :size="16" class="shrink-0 text-pine-700" /> <a :href="'mailto:' + park.org.email" class="underline">{{ park.org.email }}</a></div>
                            </div>
                        </div>

                        <a
                            v-if="mapUrl"
                            :href="mapUrl"
                            target="_blank"
                            rel="noopener"
                            class="flex items-center justify-center gap-2.5 rounded-2xl bg-pine-700 p-5 text-center font-bold text-white transition hover:bg-pine-800"
                        ><Icon name="map" :size="20" /> {{ t('common.viewOnMap') }}</a>

                        <div v-if="siblings.length" class="rounded-2xl border border-stone-100 bg-white p-5 shadow-sm">
                            <h2 class="text-sm font-bold text-pine-900">Тухайн ХЗ-нд харьяалагдах бусад ТХГ</h2>
                            <div class="mt-3 grid gap-2">
                                <router-link
                                    v-for="s in siblings"
                                    :key="s.id"
                                    :to="`/parks/${s.id}`"
                                    class="rounded-xl bg-stone-50 px-3 py-2 text-sm text-stone-700 transition hover:bg-pine-50 hover:text-pine-800"
                                >{{ s.name }} <span class="text-xs text-stone-400">({{ s.aimag }})</span></router-link>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>
</template>
