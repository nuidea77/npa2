<script setup>
import { computed, ref } from 'vue';
import axios from '../bootstrap';
import { t } from '../i18n';
import { useAuthStore } from '../stores/auth';
import Modal from './Modal.vue';
import Icon from './Icon.vue';

/** Хуудасны доод хэсгийн «Санал хүсэлт илгээх» хэсэг (nationalparkacademy.org загвар). */
const auth = useAuthStore();
const types = computed(() => auth.settings?.feedback_types ?? ['Санал, хүсэлт', 'Гомдол', 'Талархал']);
const subtypes = computed(() => auth.settings?.feedback_subtypes ?? []);

const form = ref({ type: '', subtype: '', email: auth.user?.email ?? '', phone: '', message: '' });
const errors = ref({});
const sending = ref(false);
const successMsg = ref('');

async function submit() {
    sending.value = true;
    errors.value = {};
    try {
        const { data } = await axios.post('/api/feedback', {
            type: form.value.type || types.value[0],
            subtype: form.value.subtype || null,
            message: form.value.message,
            email: form.value.email,
            phone: form.value.phone || null,
        });
        successMsg.value = data.message;
        form.value = { type: '', subtype: '', email: auth.user?.email ?? '', phone: '', message: '' };
    } catch (e) {
        errors.value = e.response?.data?.errors ?? {};
    } finally {
        sending.value = false;
    }
}
</script>

<template>
    <section class="border-t border-stone-200 py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-12 text-center">
                <h2 class="mb-4 text-3xl font-bold text-stone-900 sm:text-4xl">{{ t('feedback.title') }}</h2>
                <p class="mx-auto max-w-2xl text-lg text-stone-500">{{ t('feedback.helper') }}</p>
            </div>

            <form class="mx-auto grid max-w-3xl gap-5" @submit.prevent="submit">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="label">{{ t('feedback.type') }} <span class="text-red-500">*</span></label>
                        <select v-model="form.type" class="input w-full">
                            <option value="" disabled>{{ t('feedback.choose') }}</option>
                            <option v-for="ty in types" :key="ty" :value="ty">{{ ty }}</option>
                        </select>
                        <p v-if="errors.type" class="err">{{ errors.type[0] }}</p>
                    </div>
                    <div>
                        <label class="label">{{ t('feedback.subtype') }}</label>
                        <select v-model="form.subtype" class="input w-full">
                            <option value="" disabled>{{ t('feedback.choose') }}</option>
                            <option v-for="st in subtypes" :key="st" :value="st">{{ st }}</option>
                        </select>
                    </div>
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="label">{{ t('feedback.email') }} <span class="text-red-500">*</span></label>
                        <input v-model="form.email" type="email" class="input w-full" :placeholder="t('feedback.emailPh')" />
                        <p v-if="errors.email" class="err">{{ errors.email[0] }}</p>
                    </div>
                    <div>
                        <label class="label">{{ t('feedback.phone') }}</label>
                        <input v-model="form.phone" class="input w-full" :placeholder="t('feedback.phonePh')" />
                        <p v-if="errors.phone" class="err">{{ errors.phone[0] }}</p>
                    </div>
                </div>
                <div>
                    <label class="label">{{ t('feedback.message') }} <span class="text-red-500">*</span></label>
                    <textarea v-model="form.message" rows="5" class="input w-full" :placeholder="t('feedback.messagePh')"></textarea>
                    <p v-if="errors.message" class="err">{{ errors.message[0] }}</p>
                </div>
                <div class="text-center">
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-pine-700 px-8 py-3 text-base font-medium text-white transition-all hover:bg-pine-700/90" :disabled="sending">
                        {{ sending ? t('common.loading') : t('common.send2') }}
                    </button>
                </div>
            </form>
        </div>

        <Modal :show="!!successMsg" @close="successMsg = ''">
            <div class="text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-pine-100 text-pine-700"><Icon name="check" :size="30" /></div>
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
