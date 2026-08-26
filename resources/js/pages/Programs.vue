<script setup>
import { locale, t } from '../i18n';
import { programs } from '../content/programs';
import PageHero from '../components/PageHero.vue';
import Icon from '../components/Icon.vue';
</script>

<template>
    <div>
        <PageHero :title="t('nav.programs')" />
        <div class="mx-auto max-w-7xl px-4 py-12">
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <router-link
                    v-for="p in programs"
                    :key="p.slug"
                    :to="p.path"
                    class="group rounded-2xl border border-stone-200 bg-white p-6 transition hover:-translate-y-0.5 hover:border-pine-300 hover:shadow-md"
                >
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-pine-100 text-pine-800">
                        <Icon :name="p.icon" :size="26" />
                    </div>
                    <h3 class="mt-4 text-lg font-bold leading-snug text-pine-900 group-hover:text-pine-700">{{ t(p.key) }}</h3>
                    <p class="mt-2 text-sm text-stone-500">{{ p.short[locale] || p.short.mn }}</p>
                    <span class="mt-3 inline-block text-sm font-semibold text-pine-600">{{ t('common.readMore') }} →</span>
                </router-link>
            </div>
        </div>
    </div>
</template>
