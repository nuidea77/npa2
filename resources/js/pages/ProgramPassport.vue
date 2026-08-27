<script setup>
import { t } from '../i18n';
import DetailHeader from '../components/DetailHeader.vue';
import FeedbackInline from '../components/FeedbackInline.vue';
</script>

<template>
    <div>
        <DetailHeader
            :title="t('prog.passport')"
            back="/programs"
            :date-label="t('detail.updated')"
            date="2024/10/11"
        />

        <div class="mx-auto max-w-5xl px-4 pb-14">
            <!-- Figma: текст → том зураг → текст → 2 зураг -->
            <p class="mt-6 leading-relaxed text-stone-600">
                National Park Academy хөтөлбөрөөс жил бүр Хамгаалалтын захиргаадад NPA Паспорт гардуулна. Тухайн жилд
                зохион байгуулагдах бүхий л үйл ажиллагаанд идэвхтэй оролцсон Хамгаалалтын захиргаа паспорт дээрээ Тамга
                цуглуулах бөгөөд хамгийн олон тамга цуглуулж чадсан Хамгаалалтын захиргааг Хуралдайн үеэр шалгаруулна.
            </p>

            <img src="/images/passport.svg" alt="NPA Passport" class="mt-7 h-72 w-full rounded-xl object-cover md:h-[420px]" />

            <p class="mt-7 leading-relaxed text-stone-600">
                2024 оны NPA Паспорт дээрээ хамгийн олон тамга цуглуулж чадсан Хамгаалалтын захиргааны төлөөлөл 2025 оны
                Хуралдайн үеэр шалгарах ба Америкийн Нэгдсэн Улсын Тусгай хамгаалалттай газруудад туршлага судлах аяллын
                эзэн болно.
            </p>

            <div class="mt-7 grid gap-6 sm:grid-cols-2">
                <img src="/images/yosemite.jpg" alt="Yosemite National Park" class="h-64 w-full rounded-xl object-cover md:h-80" />
                <img src="/images/denali.jpg" alt="Denali National Park" class="h-64 w-full rounded-xl object-cover md:h-80" />
            </div>

            <div class="mt-8 rounded-xl bg-stone-100 p-5 text-sm leading-relaxed text-stone-600">
                Нэвтэрсэн ТХГ-ын ажилтнууд өөрийн Хамгаалалтын захиргааны цуглуулсан тамгыг
                <router-link to="/dashboard" class="font-semibold text-pine-700 underline">Миний булан</router-link>
                хэсгээс харах боломжтой.
            </div>
        </div>

        <FeedbackInline />
    </div>
</template>
