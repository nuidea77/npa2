<script setup>
import { nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
import axios from '../bootstrap';
import { locale, t } from '../i18n';
import Icon from './Icon.vue';

/**
 * «NPA туслах» — FAQ дээр суурилсан энгийн туслах чат.
 */
const open = ref(false);
const faqs = ref([]);
const input = ref('');
const listEl = ref(null);
const messages = ref([
    {
        from: 'bot',
        text: locale.value === 'en'
            ? 'Hello! I am the NPA assistant. Ask me about the National Park Academy, or pick a question below.'
            : 'Сайн байна уу! Би NPA туслах байна. National Park Academy-ийн талаар асуугаарай, эсвэл доорх асуултуудаас сонгоно уу.',
    },
]);

onMounted(() => window.addEventListener('npa-open-chat', onExternalOpen));
onBeforeUnmount(() => window.removeEventListener('npa-open-chat', onExternalOpen));

function onExternalOpen() {
    if (!open.value) toggleOpen();
    window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
}

async function toggleOpen() {
    open.value = !open.value;
    if (open.value && faqs.value.length === 0) {
        try {
            const { data } = await axios.get('/api/faqs');
            faqs.value = data.faqs;
        } catch { /* хоосон байж болно */ }
    }
}

function answerFor(text) {
    const q = text.toLowerCase();
    const scored = faqs.value.map((f) => {
        const words = (f.question + ' ' + f.answer).toLowerCase();
        let score = 0;
        for (const w of q.split(/\s+/).filter((x) => x.length > 2)) {
            if (words.includes(w)) score++;
        }
        return { f, score };
    }).sort((a, b) => b.score - a.score);

    if (scored[0] && scored[0].score > 0) {
        const f = scored[0].f;
        return locale.value === 'en' && f.answer_en ? f.answer_en : f.answer;
    }
    return locale.value === 'en'
        ? 'Sorry, I do not have an answer for that yet. Please contact us: info@mongolec.org, +976-70100426, or use the feedback form under Help.'
        : 'Уучлаарай, энэ асуултад одоогоор хариулт алга байна. Та info@mongolec.org хаягаар, +976-70100426 утсаар, эсвэл Тусламж цэсний санал хүсэлтийн маягтаар холбогдоно уу.';
}

async function send(text) {
    const msg = (text ?? input.value).trim();
    if (!msg) return;
    input.value = '';
    messages.value.push({ from: 'me', text: msg });
    messages.value.push({ from: 'bot', text: answerFor(msg) });
    await nextTick();
    listEl.value?.scrollTo({ top: listEl.value.scrollHeight, behavior: 'smooth' });
}
</script>

<template>
    <!-- Нээх товч -->
    <button
        class="fixed bottom-5 right-5 z-40 flex h-14 w-14 items-center justify-center rounded-full bg-pine-700 text-white shadow-lg transition hover:bg-pine-800"
        :title="t('footer.assistant')"
        @click="toggleOpen"
    >
        <Icon :name="open ? 'x' : 'chat'" :size="24" />
    </button>

    <!-- Чат цонх -->
    <div
        v-if="open"
        class="fixed bottom-24 right-5 z-40 flex h-[28rem] w-80 flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-2xl sm:w-96"
    >
        <div class="flex items-center gap-2 bg-pine-700 px-4 py-3 text-white">
            <img src="/images/favicon.png" alt="" class="h-7 w-7 rounded-lg" />
            <div class="text-sm font-bold">{{ t('footer.assistant') }}</div>
        </div>

        <div ref="listEl" class="flex-1 space-y-2 overflow-y-auto p-3">
            <div
                v-for="(m, i) in messages"
                :key="i"
                class="max-w-[85%] rounded-2xl px-3 py-2 text-sm leading-relaxed"
                :class="m.from === 'me' ? 'ml-auto bg-pine-700 text-white' : 'bg-stone-100 text-stone-800'"
            >{{ m.text }}</div>

            <div v-if="faqs.length" class="pt-1">
                <button
                    v-for="f in faqs.slice(0, 4)"
                    :key="f.id"
                    class="mb-1.5 block w-full rounded-xl border border-pine-200 bg-pine-50 px-3 py-2 text-left text-xs text-pine-800 transition hover:bg-pine-100"
                    @click="send(f.question)"
                >{{ f.question }}</button>
            </div>
        </div>

        <form class="flex gap-2 border-t border-stone-100 p-2" @submit.prevent="send()">
            <input
                v-model="input"
                class="flex-1 rounded-full border border-stone-200 px-3.5 py-2 text-sm outline-none focus:border-pine-400"
                :placeholder="locale === 'en' ? 'Type your question...' : 'Асуултаа бичнэ үү...'"
            />
            <button type="submit" class="flex items-center rounded-full bg-pine-700 px-4 py-2 text-sm font-semibold text-white hover:bg-pine-800"><Icon name="arrow-right" :size="16" /></button>
        </form>
    </div>
</template>
