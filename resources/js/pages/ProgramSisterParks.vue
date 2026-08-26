<script setup>
import { t } from '../i18n';
import PageHero from '../components/PageHero.vue';
</script>

<template>
    <div>
        <PageHero :title="t('prog.sister')" />

        <div class="mx-auto max-w-4xl px-4 py-12">
            <div class="prose-mn space-y-4 leading-relaxed text-stone-700">
                <p>
                    Манай баг хамт олон АНУ болон Монгол улсын тусгай хамгаалалттай газруудын бие биедээ туршлага арга барил,
                    харилцан мэдээлэл солилцох зорилго бүхий <strong>«Эгч дүүгийн харилцаа»</strong>-г өргөжүүлэн хөгжүүлэхэд
                    голлох үүрэгтэй оролцсон.
                </p>
                <p>
                    Эгч дүүс парк байгуулахын гол зорилго нь олон улсын хамтын ажиллагааг дэмжих, хоёр талын харилцан ашиг
                    сонирхол, ач холбогдлыг нэмэгдүүлэх, ажилтан, албан хаагчдын мэргэжлийн туршлагаа солилцох, ажлын туршлагыг
                    баяжуулах, нутгийн иргэд, нөхөрлөл, түнш байгууллагуудтай хамтран бүсийн болон орон нутгийн эдийн засагт
                    хувь нэмэр оруулахад чиглэгдсэн.
                </p>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-pine-100 bg-pine-50 p-6">
                    <div class="text-3xl">🏞️</div>
                    <h3 class="mt-2 font-bold text-pine-900">Yosemite National Park ↔ Хөвсгөл нуурын БЦГ</h3>
                    <p class="mt-2 text-sm text-stone-600">2015 онд АНУ-ын Yosemite парктай эгч дүү парк болсон.</p>
                </div>
                <div class="rounded-2xl border border-sand-200 bg-sand-50 p-6">
                    <div class="text-3xl">🤝</div>
                    <h3 class="mt-2 font-bold text-sand-900">Хамтын ажиллагаа өргөжсөөр</h3>
                    <p class="mt-2 text-sm text-stone-600">
                        Шинэ эгч дүүс паркийн харилцаа тогтоох сонирхолтой Хамгаалалтын захиргаад бидэнтэй холбогдоорой:
                        info@mongolec.org
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
