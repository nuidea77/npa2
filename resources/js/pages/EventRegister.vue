<script setup>
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from '../bootstrap';
import { t } from '../i18n';
import { useAuthStore } from '../stores/auth';
import Modal from '../components/Modal.vue';
import Icon from '../components/Icon.vue';

const route = useRoute();
const router = useRouter();
const auth = useAuthStore();

const event = ref(null);
const loading = ref(true);
const answers = ref({});
const files = ref({});
const errors = ref({});
const sending = ref(false);
const successMsg = ref('');
const stateMsg = ref('');

onMounted(async () => {
    try {
        const { data } = await axios.get(`/api/events/${route.params.id}`);
        event.value = data.event;

        if (event.value.reg_state === 'not_started') stateMsg.value = t('reg.notStarted');
        else if (event.value.reg_state === 'closed') stateMsg.value = t('reg.closed');
        else if (event.value.reg_state === 'soon') stateMsg.value = t('reg.soon');

        // Нэвтрэх шаардлагатай бол нэвтрэх хуудас руу үсэргэнэ
        if (event.value.login_required && !auth.isLoggedIn) {
            router.replace({ path: '/login', query: { next: route.fullPath } });
            return;
        }

        // Олон сонголттой асуултуудыг массив болгож эхлүүлнэ
        for (const q of event.value.questions ?? []) {
            if (q.type === 'multi') answers.value[q.id] = [];
        }
    } finally {
        loading.value = false;
    }
});

function onFile(qid, e) {
    files.value[qid] = e.target.files?.[0] ?? null;
}

function wordCount(text) {
    return (text ?? '').trim().split(/\s+/).filter(Boolean).length;
}

async function submit() {
    sending.value = true;
    errors.value = {};
    try {
        const fd = new FormData();
        fd.append('answers', JSON.stringify(answers.value));
        for (const [qid, f] of Object.entries(files.value)) {
            if (f) fd.append(`file_${qid}`, f);
        }
        const { data } = await axios.post(`/api/events/${route.params.id}/register`, fd);
        successMsg.value = data.message;
    } catch (e) {
        if (e.response?.status === 401) {
            router.push({ path: '/login', query: { next: route.fullPath } });
            return;
        }
        errors.value = e.response?.data?.errors ?? {};
        if (e.response?.data?.message && !Object.keys(errors.value).length) {
            stateMsg.value = e.response.data.message;
        }
    } finally {
        sending.value = false;
    }
}

function closeSuccess() {
    successMsg.value = '';
    router.push('/dashboard');
}
</script>

<template>
    <div class="mx-auto max-w-2xl px-4 py-12">
        <div v-if="loading" class="text-stone-400">{{ t('common.loading') }}</div>

        <div v-else-if="event">
            <router-link to="/" class="text-sm font-medium text-pine-600 hover:text-pine-800">← {{ t('common.back') }}</router-link>
            <h1 class="mt-3 text-2xl font-bold text-pine-900">{{ event.title }}</h1>
            <p class="mt-1 text-sm text-stone-500">{{ event.description }}</p>

            <!-- Бүртгэл нээгдээгүй/хаагдсан -->
            <div v-if="event.reg_state !== 'open'" class="mt-8 flex items-center justify-center gap-2.5 rounded-2xl border border-sand-300 bg-sand-100 p-6 text-center font-medium text-sand-900">
                <Icon name="warning" :size="20" class="shrink-0" /> {{ stateMsg }}
            </div>

            <!-- Асуулга -->
            <form v-else class="mt-8 grid gap-5 rounded-2xl border border-stone-100 bg-white p-6 shadow-sm" @submit.prevent="submit">
                <div v-for="q in event.questions" :key="q.id">
                    <label class="mb-1.5 block text-sm font-semibold text-stone-700">
                        {{ q.label }} <span v-if="q.required" class="text-red-500">*</span>
                    </label>

                    <!-- Тийм/Үгүй -->
                    <div v-if="q.type === 'boolean'" class="flex gap-4">
                        <label class="flex items-center gap-2 text-sm">
                            <input v-model="answers[q.id]" type="radio" value="Тийм" class="accent-pine-700" /> {{ t('common.yes') }}
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input v-model="answers[q.id]" type="radio" value="Үгүй" class="accent-pine-700" /> {{ t('common.no') }}
                        </label>
                    </div>

                    <!-- Текст хариулт -->
                    <div v-else-if="q.type === 'text'">
                        <textarea v-model="answers[q.id]" rows="4" class="input w-full"></textarea>
                        <div v-if="q.maxWords" class="mt-1 text-right text-xs" :class="wordCount(answers[q.id]) > q.maxWords ? 'text-red-500' : 'text-stone-400'">
                            {{ wordCount(answers[q.id]) }} / {{ q.maxWords }} үг
                        </div>
                    </div>

                    <!-- Линк -->
                    <input v-else-if="q.type === 'link'" v-model="answers[q.id]" type="url" class="input w-full" placeholder="https://..." />

                    <!-- Зураг/файл -->
                    <input v-else-if="q.type === 'image'" type="file" accept="image/*,.pdf" class="input w-full" @change="onFile(q.id, $event)" />

                    <!-- Нэг сонголт -->
                    <div v-else-if="q.type === 'single'" class="grid gap-2">
                        <label v-for="opt in q.options" :key="opt" class="flex items-center gap-2 rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm transition has-checked:border-pine-400 has-checked:bg-pine-50">
                            <input v-model="answers[q.id]" type="radio" :value="opt" class="accent-pine-700" /> {{ opt }}
                        </label>
                    </div>

                    <!-- Олон сонголт -->
                    <div v-else-if="q.type === 'multi'" class="grid gap-2">
                        <label v-for="opt in q.options" :key="opt" class="flex items-center gap-2 rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm transition has-checked:border-pine-400 has-checked:bg-pine-50">
                            <input v-model="answers[q.id]" type="checkbox" :value="opt" class="accent-pine-700" /> {{ opt }}
                        </label>
                    </div>

                    <p v-if="errors[q.id]" class="mt-1 text-xs text-red-600">{{ errors[q.id][0] ?? errors[q.id] }}</p>
                </div>

                <p class="text-xs text-stone-400">* Мэдээлэл дутуу бол өргөдлөө илгээх боломжгүй.</p>

                <button type="submit" class="rounded-full bg-pine-700 py-3 font-semibold text-white transition hover:bg-pine-800" :disabled="sending">
                    {{ sending ? t('common.loading') : t('common.send') }}
                </button>
            </form>
        </div>

        <Modal :show="!!successMsg" @close="closeSuccess">
            <div class="text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-pine-100 text-pine-700"><Icon name="check" :size="30" /></div>
                <p class="mt-3 font-medium text-stone-700">{{ successMsg }}</p>
            </div>
            <template #actions>
                <button class="rounded-full bg-pine-700 px-6 py-2 text-sm font-semibold text-white hover:bg-pine-800" @click="closeSuccess">
                    {{ t('common.understood') }}
                </button>
            </template>
        </Modal>
    </div>
</template>

<style scoped>
@reference '../../css/app.css';

.input { @apply rounded-xl border border-stone-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-pine-400; }
</style>
