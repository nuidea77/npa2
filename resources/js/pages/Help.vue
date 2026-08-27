<script setup>
import { computed, onMounted, ref } from 'vue';
import axios from '../bootstrap';
import { locale, t } from '../i18n';
import { useAuthStore } from '../stores/auth';
import PageHero from '../components/PageHero.vue';
import Modal from '../components/Modal.vue';
import Icon from '../components/Icon.vue';

const auth = useAuthStore();
const faqs = ref([]);
const openFaq = ref(null);

const types = computed(() => auth.settings?.feedback_types ?? []);
const subtypes = computed(() => auth.settings?.feedback_subtypes ?? []);

const form = ref({ type: '', subtype: '', message: '', email: '', phone: '' });
const errors = ref({});
const sending = ref(false);
const successMsg = ref('');

onMounted(async () => {
    const { data } = await axios.get('/api/faqs');
    faqs.value = data.faqs;
    if (auth.user) form.value.email = auth.user.email;
});

function q(f) { return locale.value === 'en' && f.question_en ? f.question_en : f.question; }
function a(f) { return locale.value === 'en' && f.answer_en ? f.answer_en : f.answer; }

async function submit() {
    sending.value = true;
    errors.value = {};
    try {
        const { data } = await axios.post('/api/feedback', form.value);
        successMsg.value = data.message;
        form.value = { type: '', subtype: '', message: '', email: auth.user?.email ?? '', phone: '' };
    } catch (e) {
        errors.value = e.response?.data?.errors ?? {};
        if (e.response?.status === 422 && !Object.keys(errors.value).length) {
            errors.value = { message: [e.response.data.message] };
        }
    } finally {
        sending.value = false;
    }
}
</script>

<template>
    <div>
        <PageHero :title="t('nav.help')" />

        <div class="mx-auto max-w-4xl px-4 py-12">
            <!-- Түгээмэл асуулт -->
            <section>
                <h2 class="text-xl font-bold text-pine-900 md:text-2xl">Түгээмэл асуулт</h2>
                <div class="mt-5 grid gap-3">
                    <div
                        v-for="f in faqs"
                        :key="f.id"
                        class="overflow-hidden rounded-2xl border border-stone-100 bg-white shadow-sm"
                    >
                        <button
                            class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left font-semibold text-stone-800 transition hover:bg-pine-50"
                            @click="openFaq = openFaq === f.id ? null : f.id"
                        >
                            {{ q(f) }}
                            <span class="text-pine-600">{{ openFaq === f.id ? '−' : '+' }}</span>
                        </button>
                        <p v-if="openFaq === f.id" class="border-t border-stone-100 px-5 py-4 text-sm leading-relaxed text-stone-600">
                            {{ a(f) }}
                        </p>
                    </div>
                </div>
            </section>

            <!-- Санал хүсэлт илгээх булан -->
            <section id="feedback" class="mt-14 scroll-mt-24">
                <h2 class="text-xl font-bold text-pine-900 md:text-2xl">{{ t('nav.feedback') }}</h2>
                <form class="mt-5 grid gap-4 rounded-2xl border border-pine-100 bg-white p-6 shadow-sm" @submit.prevent="submit">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="label">Санал хүсэлтийн төрөл *</label>
                            <select v-model="form.type" class="input w-full">
                                <option value="" disabled>{{ t('common.select') }}</option>
                                <option v-for="ty in types" :key="ty" :value="ty">{{ ty }}</option>
                            </select>
                            <p v-if="errors.type" class="err">{{ errors.type[0] }}</p>
                        </div>
                        <div>
                            <label class="label">Дэд сэдэв</label>
                            <select v-model="form.subtype" class="input w-full">
                                <option value="">{{ t('common.select') }}</option>
                                <option v-for="st in subtypes" :key="st" :value="st">{{ st }}</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="label">Санал хүсэлт *</label>
                        <textarea v-model="form.message" rows="5" class="input w-full" placeholder="Санал хүсэлтээ энд бичнэ үү..."></textarea>
                        <p v-if="errors.message" class="err">{{ errors.message[0] }}</p>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="label">И-мэйл хаяг *</label>
                            <input v-model="form.email" type="email" class="input w-full" placeholder="tanii@mail.mn" />
                            <p v-if="errors.email" class="err">{{ errors.email[0] }}</p>
                        </div>
                        <div>
                            <label class="label">Утасны дугаар</label>
                            <input v-model="form.phone" class="input w-full" placeholder="99112233" />
                            <p v-if="errors.phone" class="err">{{ errors.phone[0] }}</p>
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="rounded-full bg-pine-700 px-7 py-2.5 font-semibold text-white transition hover:bg-pine-800" :disabled="sending">
                            {{ sending ? t('common.loading') : t('common.send') }}
                        </button>
                    </div>
                </form>
            </section>
        </div>

        <Modal :show="!!successMsg" @close="successMsg = ''">
            <div class="text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-pine-100 text-pine-700"><Icon name="check" :size="30" /></div>
                <p class="mt-3 font-medium text-stone-700">{{ successMsg }}</p>
            </div>
        </Modal>
    </div>
</template>

<style scoped>
@reference '../../css/app.css';

.label {
    @apply mb-1 block text-sm font-medium text-stone-600;
}
.input {
    @apply rounded-xl border border-stone-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-pine-400;
}
.err {
    @apply mt-1 text-xs text-red-600;
}
</style>
