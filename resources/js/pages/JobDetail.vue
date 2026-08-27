<script setup>
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import axios from '../bootstrap';
import { t } from '../i18n';
import Icon from '../components/Icon.vue';
import DetailHeader from '../components/DetailHeader.vue';
import FeedbackInline from '../components/FeedbackInline.vue';

const route = useRoute();
const job = ref(null);
const loading = ref(true);

onMounted(async () => {
    try {
        const { data } = await axios.get(`/api/jobs/${route.params.id}`);
        job.value = data.job;
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <div>
        <div v-if="loading" class="mx-auto max-w-4xl px-4 py-16 text-stone-400">{{ t('common.loading') }}</div>

        <template v-else-if="job">
        <DetailHeader :title="job.position" back="/jobs" :date="job.open_date?.slice(0, 10).replaceAll('-', '/')" />
        <div class="mx-auto max-w-4xl px-4 pb-14">
            <div class="flex flex-wrap items-center gap-2">
                <span
                    class="rounded-full px-3 py-1 text-xs font-bold"
                    :class="job.status === 'open' ? 'bg-pine-100 text-pine-800' : 'bg-red-100 text-red-700'"
                >{{ job.status === 'open' ? t('common.open') : t('common.closed') }}</span>
                <span class="rounded-full bg-stone-100 px-3 py-1 text-xs font-medium text-stone-600">{{ job.contract_type }}</span>
                <span class="text-xs text-stone-400">{{ job.open_date }} — {{ job.close_date }}</span>
            </div>

            <div class="mt-3 text-stone-600">{{ job.park_name }} — {{ job.org?.name }}</div>

            <div class="mt-8 grid gap-6 md:grid-cols-3">
                <div class="md:col-span-2">
                    <section class="card">
                        <h2 class="card-h">Ажлын байрны тодорхойлолт</h2>
                        <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-stone-600">{{ job.description }}</p>
                    </section>
                    <section class="card mt-4">
                        <h2 class="card-h">Тавигдах шаардлага</h2>
                        <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-stone-600">{{ job.requirements }}</p>
                    </section>
                    <section class="card mt-4">
                        <h2 class="card-h">Бүрдүүлэх материал</h2>
                        <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-stone-600">{{ job.materials }}</p>
                    </section>
                </div>
                <aside>
                    <div class="card border-pine-200 bg-pine-50">
                        <h2 class="card-h">Холбоо барих</h2>
                        <div class="mt-3 space-y-2.5 text-sm text-stone-700">
                            <div class="flex items-start gap-2"><Icon name="building" :size="16" class="mt-0.5 shrink-0 text-pine-700" /> {{ job.org?.name }}</div>
                            <div v-if="job.org?.address" class="flex items-start gap-2"><Icon name="map-pin" :size="16" class="mt-0.5 shrink-0 text-pine-700" /> {{ job.org.address }}</div>
                            <div class="flex items-center gap-2"><Icon name="phone" :size="16" class="shrink-0 text-pine-700" /> {{ job.phone || job.org?.phone }}</div>
                            <div class="flex items-center gap-2"><Icon name="mail" :size="16" class="shrink-0 text-pine-700" /> <a :href="'mailto:' + (job.email || job.org?.email)" class="underline">{{ job.email || job.org?.email }}</a></div>
                        </div>
                        <p class="mt-4 text-xs text-stone-500">
                            Материалаа дээрх хаягаар шууд илгээнэ үү — NPA вэбээр хүсэлт ирүүлэх шаардлагагүй.
                        </p>
                    </div>
                </aside>
            </div>
        </div>
        <FeedbackInline />
        </template>
    </div>
</template>

<style scoped>
@reference '../../css/app.css';

.card {
    @apply rounded-2xl border border-stone-100 bg-white p-5 shadow-sm;
}
.card-h {
    @apply font-bold text-pine-900;
}
</style>
