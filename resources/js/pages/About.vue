<script setup>
import { ref } from 'vue';
import { locale, t } from '../i18n';
import { aboutText, team, timeline } from '../content/about';
import Icon from '../components/Icon.vue';
import FeedbackInline from '../components/FeedbackInline.vue';

const member = ref(null);

/** «2021 оноос...» өгүүлбэрээс догол мөр таслах */
const paragraphs = (aboutText.mn.split('. 2021 оноос').length === 2)
    ? [aboutText.mn.split('. 2021 оноос')[0] + '.', '2021 оноос' + aboutText.mn.split('. 2021 оноос')[1]]
    : [aboutText.mn];
const paragraphsEn = [aboutText.en];
</script>

<template>
    <div>
        <!-- Багийн зурагтай hero (Figma) -->
        <section class="h-64 w-full overflow-hidden md:h-96">
            <img src="/images/team.jpg" alt="National Park Academy багийн зураг" class="h-full w-full object-cover object-center" />
        </section>

        <!-- Бидний тухай — 2 багана -->
        <section id="about" class="scroll-mt-20 border-b border-stone-100">
            <div class="mx-auto grid max-w-6xl gap-8 px-4 py-14 md:grid-cols-[1fr_1.8fr] md:gap-16">
                <h1 id="mission" class="scroll-mt-24 text-3xl font-extrabold text-pine-800 md:text-4xl">{{ t('nav.about') }}</h1>
                <div class="space-y-5 leading-relaxed text-stone-600">
                    <p v-for="(p, i) in (locale === 'en' ? paragraphsEn : paragraphs)" :key="i">{{ p }}</p>
                </div>
            </div>
        </section>

        <!-- Он цагийн хэлхээс — зигзаг (Figma) -->
        <section id="timeline" class="scroll-mt-20 bg-stone-50/60">
            <div class="mx-auto max-w-5xl px-4 py-14">
                <h2 class="text-center text-2xl font-extrabold text-stone-900">{{ t('nav.about.timeline') }}</h2>

                <div class="relative mt-12">
                    <!-- Голын шугам -->
                    <div class="absolute left-4 top-0 h-full w-px bg-stone-300 md:left-1/2"></div>

                    <div class="space-y-6">
                        <div
                            v-for="(tl, i) in timeline"
                            :key="i"
                            class="relative md:grid md:grid-cols-2 md:gap-14"
                        >
                            <!-- Цэг -->
                            <span class="absolute left-4 top-6 h-2.5 w-2.5 -translate-x-1/2 rounded-full bg-pine-700 md:left-1/2"></span>

                            <div :class="i % 2 === 0 ? 'md:col-start-1' : 'md:col-start-2'" class="ml-10 md:ml-0">
                                <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-stone-100">
                                    <div class="font-extrabold text-stone-900">{{ tl.year }}{{ /он|сар|\./.test(tl.year) ? '' : ' он' }}</div>
                                    <p class="mt-1.5 text-sm leading-relaxed text-stone-500">{{ tl.text }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Хамт олон — дугуй аватарууд (Figma) -->
        <section id="team" class="scroll-mt-20">
            <div class="mx-auto max-w-6xl px-4 py-14">
                <h2 class="text-center text-2xl font-extrabold text-stone-900">{{ t('nav.about.team') }}</h2>

                <div class="mt-12 grid grid-cols-2 gap-x-6 gap-y-10 sm:grid-cols-3 lg:grid-cols-6">
                    <button
                        v-for="m in team"
                        :key="m.name"
                        class="group text-center"
                        @click="member = m"
                    >
                        <div class="mx-auto flex h-28 w-28 items-center justify-center overflow-hidden rounded-full bg-stone-200 text-stone-400 transition group-hover:ring-4 group-hover:ring-pine-100 md:h-32 md:w-32">
                            <img v-if="m.photo" :src="m.photo" :alt="m.name" class="h-full w-full object-cover" />
                            <Icon v-else name="user" :size="44" />
                        </div>
                        <div class="mt-3 font-bold text-stone-900">{{ m.name }}</div>
                        <div class="mt-0.5 text-sm text-stone-500">{{ m.role }}</div>
                    </button>
                </div>
            </div>
        </section>

        <FeedbackInline />

        <!-- Гишүүний дэлгэрэнгүй modal (Figma) -->
        <Teleport to="body">
            <div v-if="member" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/50" @click="member = null"></div>
                <div class="relative max-h-[85vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl md:p-9">
                    <button
                        class="absolute right-4 top-4 flex h-9 w-9 items-center justify-center rounded-full text-stone-400 transition hover:bg-stone-100 hover:text-stone-700"
                        @click="member = null"
                    ><Icon name="x" :size="20" /></button>

                    <div class="grid gap-7 md:grid-cols-[260px_1fr]">
                        <div class="flex h-64 items-center justify-center overflow-hidden rounded-xl bg-stone-200 text-stone-400 md:h-80">
                            <img v-if="member.photo" :src="member.photo" :alt="member.name" class="h-full w-full object-cover" />
                            <Icon v-else name="user" :size="72" />
                        </div>
                        <div>
                            <h3 class="text-xl font-extrabold text-stone-900">{{ member.name }}</h3>
                            <div class="mt-1 font-medium text-pine-700">{{ member.role }}</div>
                            <p class="prose-mn mt-5 whitespace-pre-line text-sm leading-relaxed text-stone-600">{{ member.bio }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>
