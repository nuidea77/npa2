<script setup>
import { t } from '../i18n';
import { useEvents } from '../composables/useEvents';
import PageHero from '../components/PageHero.vue';
import RegButton from '../components/RegButton.vue';

const { events, loading } = useEvents('regional');
</script>

<template>
    <div>
        <PageHero :title="t('prog.regional')" />

        <div class="mx-auto max-w-4xl px-4 py-12">
            <p class="prose-mn leading-relaxed text-stone-700">
                National Park Academy хөтөлбөр нь Тусгай хамгаалалттай газруудын мэргэжилтэн, байгаль хамгаалагчдын
                мэргэжлийн ур чадварыг сайжруулах зорилгоор Баруун, Хангайн, Төвийн, Зүүн, Говийн бүсүүдэд
                бүсчилсэн сургалтуудыг зохион байгуулдаг.
            </p>

            <div v-if="loading" class="mt-8 text-stone-400">{{ t('common.loading') }}</div>

            <div v-else-if="events.length" class="mt-8 grid gap-4">
                <div v-for="e in events" :key="e.id" class="rounded-2xl border border-pine-100 bg-white p-6 shadow-sm">
                    <div class="text-xs font-semibold uppercase tracking-wide text-pine-500">{{ e.year }}</div>
                    <h3 class="mt-1 font-bold text-stone-800">{{ e.title }}</h3>
                    <p class="mt-1 text-sm text-stone-500">{{ e.description }}</p>
                    <div class="mt-4"><RegButton :event="e" /></div>
                </div>
            </div>

            <p v-else class="mt-8 rounded-2xl bg-stone-100 p-6 text-stone-500">{{ t('common.comingSoon') }}</p>
        </div>
    </div>
</template>
