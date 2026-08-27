<script setup>
import { computed } from 'vue';
import { t } from '../i18n';
import { useEvents } from '../composables/useEvents';
import DetailHeader from '../components/DetailHeader.vue';
import FeedbackInline from '../components/FeedbackInline.vue';
import RegButton from '../components/RegButton.vue';

const { events } = useEvents('junior_ranger');
const upcoming = computed(() => events.value.filter((e) => ['open', 'not_started', 'soon'].includes(e.reg_state)));
const past = computed(() => events.value.filter((e) => e.reg_state === 'closed'));
</script>

<template>
    <div>
        <DetailHeader :title="t('prog.junior')" back="/programs" />

        <div class="mx-auto max-w-4xl px-4 py-12">
            <section>
                <h2 class="text-xl font-bold text-pine-900 md:text-2xl">Өсвөрийн байгаль хамгаалагч 2024</h2>
                <div class="prose-mn mt-4 space-y-4 leading-relaxed text-stone-700">
                    <p>
                        Монгол Экологи Төв нь «Өсвөрийн байгаль хамгаалагч» хөтөлбөрийн хүрээнд 6-8 дугаар ангийн сурагчдыг
                        хамруулсан 7-10 хоногийн хугацаатай зуны зусланд 2013 оноос нийт <strong>360 гаруй хүүхдийг</strong> хамруулж
                        байгаль хамгааллын олон талт мэдлэгийг олгож ирсэн.
                    </p>
                    <p>
                        2024 онд зохион байгуулсан 7 хоногийн зусланд Хөвсгөл аймгийн сумдаас сонгогдсон 6, 7, 8 дугаар ангийн
                        30 хүүхэд, Монгол 5, Олон улсын 5, нийт 10 багш нар хамрагдсан бөгөөд энэ удаагийн зусланд Монгол Улсын
                        Тусгай хамгаалалттай газруудын байгаль хамгаалагч, мэргэжилтнүүд оролцож хамтдаа өсвөрийн байгаль
                        хамгаалагчдыг бэлтгэх зуны зусланд туршлага судалсан билээ.
                    </p>
                    <p>
                        Оролцогч хүүхдүүд маань сонирхолтой үйл ажиллагаа, интерактив сургалт, дадлага туршлагаар дамжуулан
                        экологийн мэдлэгээ баяжуулж, хамтран ажиллах ур чадвараа сайжруулан, байгаль орчноо хамгаалах хүсэл
                        эрмэлзлээр дүүрэн болсон.
                    </p>
                </div>

                <div class="mt-6 aspect-video overflow-hidden rounded-2xl shadow">
                    <iframe
                        class="h-full w-full"
                        src="https://www.youtube.com/embed/5H6_eBvkdwo"
                        title="Зуны зуслан 2024"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                    ></iframe>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-3">
                    <img src="/images/news-camp.svg" alt="Зуслан 2024" class="h-32 w-full rounded-xl object-cover" />
                    <img src="/images/gallery-camp1.svg" alt="Зуслан 2024" class="h-32 w-full rounded-xl object-cover" />
                    <img src="/images/gallery-camp2.svg" alt="Зуслан 2024" class="h-32 w-full rounded-xl object-cover" />
                </div>
            </section>

            <section class="mt-12">
                <h2 class="text-xl font-bold text-pine-900 md:text-2xl">Бүртгэл</h2>
                <div v-if="upcoming.length" class="mt-4 grid gap-4">
                    <div v-for="e in upcoming" :key="e.id" class="rounded-2xl border border-pine-100 bg-white p-6 shadow-sm">
                        <h3 class="font-bold text-stone-800">{{ e.title }}</h3>
                        <p class="mt-1 text-sm text-stone-500">{{ e.description }}</p>
                        <div class="mt-4"><RegButton :event="e" /></div>
                    </div>
                </div>
                <div v-else-if="past.length" class="mt-4 rounded-2xl border border-stone-100 bg-white p-6 shadow-sm">
                    <h3 class="font-bold text-stone-800">{{ past[0].title }}</h3>
                    <div class="mt-4"><RegButton :event="past[0]" /></div>
                </div>
                <p v-else class="mt-4 rounded-2xl bg-stone-100 p-6 text-stone-500">{{ t('common.comingSoon') }}</p>
            </section>
        </div>
        <FeedbackInline />
    </div>
</template>
