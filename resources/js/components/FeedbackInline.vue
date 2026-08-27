<script setup>
import { ref } from 'vue';
import axios from '../bootstrap';
import { t } from '../i18n';
import { useAuthStore } from '../stores/auth';
import Modal from './Modal.vue';

/** Хуудасны доод хэсгийн «САНАЛ ХҮСЭЛТ ИЛГЭЭХ» хэсэг (Figma загварын дагуу). */
const auth = useAuthStore();
const form = ref({ email: auth.user?.email ?? '', phone: '', message: '' });
const errors = ref({});
const sending = ref(false);
const successMsg = ref('');

async function submit() {
    sending.value = true;
    errors.value = {};
    try {
        const { data } = await axios.post('/api/feedback', {
            type: 'Санал, хүсэлт',
            message: form.value.message,
            email: form.value.email,
            phone: form.value.phone || null,
        });
        successMsg.value = data.message;
        form.value = { email: auth.user?.email ?? '', phone: '', message: '' };
    } catch (e) {
        errors.value = e.response?.data?.errors ?? {};
    } finally {
        sending.value = false;
    }
}
</script>

<template>
    <section class="border-t border-stone-200 bg-white">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-14 lg:grid-cols-[1fr_1.6fr]">
            <div>
                <h2 class="text-xl font-extrabold uppercase tracking-wide text-stone-900">{{ t('feedback.title') }}</h2>
                <p class="mt-3 max-w-sm text-sm leading-relaxed text-stone-500">{{ t('feedback.helper') }}</p>
            </div>

            <form class="grid gap-4" @submit.prevent="submit">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label">{{ t('feedback.email') }} <span class="text-red-500">*</span></label>
                        <input v-model="form.email" type="email" class="input w-full" :placeholder="t('feedback.emailPh')" />
                        <p v-if="errors.email" class="err">{{ errors.email[0] }}</p>
                    </div>
                    <div>
                        <label class="label">{{ t('feedback.phone') }} <span class="text-red-500">*</span></label>
                        <input v-model="form.phone" class="input w-full" :placeholder="t('feedback.phonePh')" />
                        <p v-if="errors.phone" class="err">{{ errors.phone[0] }}</p>
                    </div>
                </div>
                <div>
                    <label class="label">{{ t('feedback.message') }} <span class="text-red-500">*</span></label>
                    <textarea v-model="form.message" rows="4" class="input w-full" :placeholder="t('feedback.messagePh')"></textarea>
                    <p v-if="errors.message" class="err">{{ errors.message[0] }}</p>
                </div>
                <div>
                    <button class="rounded-lg bg-pine-700 px-8 py-2.5 text-sm font-bold text-white transition hover:bg-pine-800" :disabled="sending">
                        {{ sending ? t('common.loading') : t('common.send2') }}
                    </button>
                </div>
            </form>
        </div>

        <Modal :show="!!successMsg" @close="successMsg = ''">
            <div class="text-center">
                <div class="text-4xl">✅</div>
                <p class="mt-3 font-medium text-stone-700">{{ successMsg }}</p>
            </div>
        </Modal>
    </section>
</template>

<style scoped>
@reference '../../css/app.css';

.label { @apply mb-1.5 block text-sm font-semibold text-stone-700; }
.input { @apply rounded-lg border border-stone-300 bg-white px-3.5 py-2.5 text-sm outline-none transition placeholder:text-stone-400 focus:border-pine-500; }
.err { @apply mt-1 text-xs text-red-600; }
</style>
