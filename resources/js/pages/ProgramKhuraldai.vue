<script setup>
import { computed } from 'vue';
import { t } from '../i18n';
import { useEvents } from '../composables/useEvents';
import PageHero from '../components/PageHero.vue';
import RegButton from '../components/RegButton.vue';

const { events } = useEvents('khuraldai');
const latest = computed(() => events.value[0] ?? null);
const upcoming = computed(() => events.value.filter((e) => ['open', 'not_started', 'soon'].includes(e.reg_state)));
</script>

<template>
    <div>
        <PageHero :title="t('prog.khuraldai')" />

        <div class="mx-auto max-w-4xl px-4 py-12">
            <!-- Хуралдай 2024 -->
            <section>
                <h2 class="text-xl font-bold text-pine-900 md:text-2xl">Хуралдай 2024</h2>
                <div class="prose-mn mt-4 space-y-4 leading-relaxed text-stone-700">
                    <p>
                        Хамгаалалтын захиргаадыг бэхжүүлэх зорилготой <strong>«ТУСГАЙ ХАМГААЛАЛТТАЙ ГАЗАР – ҮНДЭСНИЙ БАХАРХАЛ»</strong>
                        хуралдайг Дөрөвдүгээр сарын 25, 26-ны өдрүүдэд Улаанбаатар хотод зохион байгууллаа.
                    </p>
                    <p>
                        Тус хуралдайд Монгол улсын өнцөг булан бүрээс мэргэжил нэгтнүүдийнхээ төлөөлөл болсон Улсын Тусгай
                        Хамгаалалттай Газруудын Хамгаалалтын захиргаадын 30 байгаль хамгаалагч, мэргэжилтнүүд,
                        YSC (Youth Sustainability Corps) Залуучуудын Тогтвортой Хөгжлийн Корпус хөтөлбөрийн элч төлөөлөгч нар оролцсон юм.
                    </p>
                    <p>
                        Тус хуралдайгаар «Хувь хүний хөгжил ба нөлөөлөл», «Тусгай хамгаалалттай газар ба гийчдийн удирдлага»,
                        «Тусгай хамгаалалттай газар аялагчийн нүдээр», «Сошиал медиа хэрэглээний чиг хандлага»,
                        «Тусгай хамгаалалттай газруудын тэмдэг тэмдэглэгээ», мэргэжилтэн, байгаль хамгаалагчдын хэлэлцүүлэг
                        зэрэг олон сонирхолтой сэдвээр зочин илтгэгч нар илтгэлээ тавьсан билээ.
                    </p>
                </div>

                <!-- Бичлэг -->
                <div class="mt-6 aspect-video overflow-hidden rounded-2xl shadow">
                    <iframe
                        class="h-full w-full"
                        src="https://www.youtube.com/embed/SPzzrCyI0nk"
                        title="Хуралдай 2024"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                    ></iframe>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-3">
                    <img src="/images/news-khuraldai.svg" alt="Хуралдай 2024" class="h-32 w-full rounded-xl object-cover" />
                    <img src="/images/gallery-forum1.svg" alt="Хуралдай 2024" class="h-32 w-full rounded-xl object-cover" />
                    <img src="/images/gallery-forum2.svg" alt="Хуралдай 2024" class="h-32 w-full rounded-xl object-cover" />
                </div>
            </section>

            <!-- Дараагийн хуралдайнууд -->
            <section class="mt-12">
                <h2 class="text-xl font-bold text-pine-900 md:text-2xl">Дараагийн хуралдай</h2>
                <div v-if="upcoming.length" class="mt-4 grid gap-4">
                    <div v-for="e in upcoming" :key="e.id" class="rounded-2xl border border-pine-100 bg-white p-6 shadow-sm">
                        <h3 class="font-bold text-stone-800">{{ e.title }}</h3>
                        <p class="mt-1 text-sm text-stone-500">{{ e.description }}</p>
                        <div class="mt-4"><RegButton :event="e" /></div>
                    </div>
                </div>
                <p v-else class="mt-4 rounded-2xl bg-stone-100 p-6 text-stone-500">{{ t('common.comingSoon') }}</p>

                <!-- Хамгийн сүүлийн (хаагдсан) бүртгэл харуулах -->
                <div v-if="latest && !upcoming.length" class="mt-4 rounded-2xl border border-stone-100 bg-white p-6 shadow-sm">
                    <h3 class="font-bold text-stone-800">{{ latest.title }}</h3>
                    <div class="mt-4"><RegButton :event="latest" /></div>
                </div>
            </section>
        </div>
    </div>
</template>
