<script setup>
import { onMounted, ref } from 'vue';
import axios from '../bootstrap';
import { t } from '../i18n';
import PageHero from '../components/PageHero.vue';

const jobs = ref([]);
const loading = ref(true);

onMounted(async () => {
    try {
        const { data } = await axios.get('/api/jobs');
        jobs.value = data.jobs;
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <div>
        <PageHero
            :title="t('prog.jobs')"
            subtitle="Нээлттэй ажлын байр байгаа хамгаалалтын захиргаад. Зар дээр тухайн захиргааны утас, и-мэйл, бүрдүүлэх материал байгаа тул шууд холбогдоно уу."
        />

        <div class="mx-auto max-w-5xl px-4 py-12">
            <div v-if="loading" class="text-stone-400">{{ t('common.loading') }}</div>
            <p v-else-if="!jobs.length" class="rounded-2xl bg-stone-100 p-6 text-stone-500">{{ t('common.empty') }}</p>

            <div v-else class="grid gap-4">
                <router-link
                    v-for="j in jobs"
                    :key="j.id"
                    :to="`/jobs/${j.id}`"
                    class="group rounded-2xl border border-stone-100 bg-white p-5 shadow-sm transition hover:border-pine-200 hover:shadow-md"
                >
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-bold text-stone-800 group-hover:text-pine-700">{{ j.position }}</h3>
                            <div class="mt-1 text-sm text-stone-500">{{ j.park_name }} — {{ j.org?.name }}</div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-medium text-stone-600">{{ j.contract_type }}</span>
                            <span
                                class="rounded-full px-3 py-1 text-xs font-bold"
                                :class="j.status === 'open' ? 'bg-pine-100 text-pine-800' : 'bg-red-100 text-red-700'"
                            >{{ j.status === 'open' ? t('common.open') : t('common.closed') }}</span>
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-stone-400">
                        {{ t('common.date') }}: {{ j.open_date }} — {{ j.close_date }}
                    </div>
                </router-link>
            </div>
        </div>
    </div>
</template>
