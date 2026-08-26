<script setup>
import { ref } from 'vue';
import axios from '../bootstrap';
import { t } from '../i18n';

const email = ref('');
const message = ref('');
const errors = ref({});
const loading = ref(false);

async function submit() {
    loading.value = true;
    errors.value = {};
    message.value = '';
    try {
        const { data } = await axios.post('/api/auth/forgot', { email: email.value });
        message.value = data.message;
    } catch (e) {
        errors.value = e.response?.data?.errors ?? {};
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div class="mx-auto max-w-md px-4 py-16">
        <div class="rounded-2xl border border-stone-100 bg-white p-8 shadow-sm">
            <h1 class="text-xl font-bold text-pine-900">{{ t('auth.forgot') }}</h1>
            <p class="mt-2 text-sm text-stone-500">
                Бүртгэлтэй и-мэйл хаягаа оруулна уу. Супер админ таны нууц үгийг сэргээж, и-мэйлээр мэдэгдэх болно.
            </p>

            <form class="mt-6 grid gap-4" @submit.prevent="submit">
                <div>
                    <label class="mb-1 block text-sm font-medium text-stone-600">{{ t('auth.email') }}</label>
                    <input v-model="email" type="email" class="w-full rounded-xl border border-stone-200 px-3.5 py-2.5 text-sm outline-none focus:border-pine-400" placeholder="tanii@mail.mn" />
                    <p v-if="errors.email" class="mt-1 text-xs text-red-600">{{ errors.email[0] }}</p>
                </div>
                <button class="rounded-xl bg-pine-700 py-2.5 font-semibold text-white transition hover:bg-pine-800" :disabled="loading">
                    {{ loading ? t('common.loading') : t('common.send') }}
                </button>
            </form>

            <p v-if="message" class="mt-4 rounded-xl bg-pine-50 p-3 text-sm text-pine-800">{{ message }}</p>

            <div class="mt-5 text-center text-sm">
                <router-link to="/login" class="font-medium text-pine-600 hover:text-pine-800">← {{ t('auth.loginTitle') }}</router-link>
            </div>
        </div>
    </div>
</template>
