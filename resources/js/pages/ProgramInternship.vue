<script setup>
import { t } from '../i18n';
import PageHero from '../components/PageHero.vue';
import Icon from '../components/Icon.vue';
</script>

<template>
    <div>
        <PageHero :title="t('prog.internship')" />

        <div class="mx-auto max-w-4xl px-4 py-12">
            <div class="prose-mn space-y-4 leading-relaxed text-stone-700">
                <p>
                    Байгаль орчин, экологи, ой, газарзүй, аялал жуулчлалын чиглэлээр суралцаж буй оюутнууд Монгол Улсын
                    Тусгай хамгаалалттай газруудын Хамгаалалтын захиргаадад дадлага хийх боломжтой.
                </p>
                <p>
                    Дадлагын хугацаанд оюутнууд байгаль хамгаалагч, мэргэжилтнүүдтэй хамтран ажиллаж, ТХГ-ын менежмент,
                    хяналт шалгалт, гийчдийн удирдлага, судалгааны ажлын бодит туршлага хуримтлуулна.
                </p>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-2">
                <router-link to="/jobs" class="rounded-2xl border border-stone-200 bg-white p-6 transition hover:shadow-md">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-pine-100 text-pine-800"><Icon name="briefcase" :size="22" /></div>
                    <h3 class="mt-2 font-bold text-pine-900">{{ t('prog.jobs') }}</h3>
                    <p class="mt-2 text-sm text-stone-500">Дадлагажигч оюутны зар нээлттэй ажлын байрны хэсэгт нийтлэгддэг.</p>
                    <span class="mt-3 inline-block text-sm font-semibold text-pine-600">{{ t('common.readMore') }} →</span>
                </router-link>
                <router-link to="/programs/volunteer" class="rounded-2xl border border-stone-200 bg-white p-6 transition hover:shadow-md">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-pine-100 text-pine-800"><Icon name="sprout" :size="22" /></div>
                    <h3 class="mt-2 font-bold text-pine-900">{{ t('prog.volunteer') }}</h3>
                    <p class="mt-2 text-sm text-stone-500">Сайн дурын ажлаар мөн адил үнэтэй туршлага хуримтлуулах боломжтой.</p>
                    <span class="mt-3 inline-block text-sm font-semibold text-pine-600">{{ t('common.readMore') }} →</span>
                </router-link>
            </div>
        </div>
    </div>
</template>
