<script setup>
import { t } from '../i18n';
import PageHero from '../components/PageHero.vue';
</script>

<template>
    <div>
        <PageHero :title="t('prog.passport')" />

        <div class="mx-auto max-w-4xl px-4 py-12">
            <img src="/images/passport.svg" alt="NPA Passport" class="mx-auto w-full max-w-md rounded-2xl shadow" />

            <div class="prose-mn mt-8 space-y-4 leading-relaxed text-stone-700">
                <p>
                    National Park Academy хөтөлбөрөөс жил бүр Хамгаалалтын захиргаадад <strong>NPA Паспорт</strong> гардуулна.
                    Тухайн жилд зохион байгуулагдах бүхий л үйл ажиллагаанд идэвхтэй оролцсон Хамгаалалтын захиргаа
                    паспорт дээрээ <strong>Тамга</strong> цуглуулах бөгөөд хамгийн олон тамга цуглуулж чадсан Хамгаалалтын
                    захиргааг Хуралдайн үеэр шалгаруулна.
                </p>
                <p class="rounded-2xl border border-sand-200 bg-sand-50 p-5 font-medium text-sand-900">
                    🏆 Паспорт дээрээ хамгийн олон тамга цуглуулж чадсан Хамгаалалтын захиргааны төлөөлөл Хуралдайн үеэр
                    шалгарч, <strong>Америкийн Нэгдсэн Улсын Тусгай хамгаалалттай газруудад туршлага судлах аяллын эзэн</strong> болно.
                </p>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-2">
                <figure class="overflow-hidden rounded-2xl border border-stone-100 shadow-sm">
                    <img src="/images/yosemite.svg" alt="Yosemite National Park" class="h-44 w-full object-cover" />
                    <figcaption class="bg-white p-3 text-sm font-medium text-stone-600">Yosemite National Park, АНУ</figcaption>
                </figure>
                <figure class="overflow-hidden rounded-2xl border border-stone-100 shadow-sm">
                    <img src="/images/denali.svg" alt="Denali National Park" class="h-44 w-full object-cover" />
                    <figcaption class="bg-white p-3 text-sm font-medium text-stone-600">Denali National Park, АНУ</figcaption>
                </figure>
            </div>

            <div class="mt-10 rounded-2xl bg-pine-50 p-6 text-sm text-pine-900">
                💡 Нэвтэрсэн ТХГ-ын ажилтнууд өөрийн Хамгаалалтын захиргааны цуглуулсан тамгыг
                <router-link to="/dashboard" class="font-semibold underline">Миний булан</router-link> хэсгээс харах боломжтой.
            </div>
        </div>
    </div>
</template>
