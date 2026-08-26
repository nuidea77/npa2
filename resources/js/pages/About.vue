<script setup>
import { locale, t } from '../i18n';
import { aboutText, mission, team, timeline } from '../content/about';
import PageHero from '../components/PageHero.vue';
</script>

<template>
    <div>
        <PageHero :title="t('nav.about')" :subtitle="mission[locale] || mission.mn" />

        <div class="mx-auto max-w-5xl px-4 py-12">
            <!-- Зорилго -->
            <section id="mission" class="scroll-mt-24">
                <h2 class="section-h">{{ t('nav.about.mission') }}</h2>
                <p class="mt-4 rounded-2xl border-l-4 border-pine-600 bg-pine-50 p-5 text-lg font-medium leading-relaxed text-pine-900">
                    {{ mission[locale] || mission.mn }}
                </p>
            </section>

            <!-- Бидний тухай -->
            <section id="about" class="mt-14 scroll-mt-24">
                <h2 class="section-h">{{ t('nav.about.about') }}</h2>
                <p class="prose-mn mt-4 leading-relaxed text-stone-700">{{ aboutText[locale] || aboutText.mn }}</p>
            </section>

            <!-- Он цагийн хэлхээс -->
            <section id="timeline" class="mt-14 scroll-mt-24">
                <h2 class="section-h">{{ t('nav.about.timeline') }}</h2>
                <ol class="relative mt-8 border-l-2 border-pine-200 pl-6">
                    <li v-for="(tl, i) in timeline" :key="i" class="relative mb-7">
                        <span class="absolute -left-[31px] top-1 h-4 w-4 rounded-full border-4 border-pine-100 bg-pine-600"></span>
                        <div class="text-sm font-bold text-pine-700">{{ tl.year }}</div>
                        <div class="mt-0.5 text-stone-700">{{ tl.text }}</div>
                    </li>
                </ol>
            </section>

            <!-- Хамт олон -->
            <section id="team" class="mt-14 scroll-mt-24">
                <h2 class="section-h">{{ t('nav.about.team') }}</h2>
                <div class="mt-8 grid gap-6">
                    <article
                        v-for="m in team"
                        :key="m.name"
                        class="rounded-2xl border border-stone-100 bg-white p-6 shadow-sm"
                    >
                        <div class="flex items-center gap-4">
                            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-pine-100 text-3xl">
                                {{ m.gender === 'female' ? '👩' : '👨' }}
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-pine-900">{{ m.name }}</h3>
                                <div class="text-sm font-medium text-sand-700">{{ m.role }}</div>
                            </div>
                        </div>
                        <p class="prose-mn mt-4 text-sm leading-relaxed text-stone-600">{{ m.bio }}</p>
                    </article>
                </div>
            </section>
        </div>
    </div>
</template>

<style scoped>
@reference '../../css/app.css';

.section-h {
    @apply text-xl font-bold text-pine-900 md:text-2xl;
}
</style>
