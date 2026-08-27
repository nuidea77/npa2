<script setup>
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from '../bootstrap';
import { t } from '../i18n';
import { useAuthStore } from '../stores/auth';
import Logo from '../components/Logo.vue';

const auth = useAuthStore();
const route = useRoute();
const router = useRouter();

const form = ref({ email: '', password: '', remember: true });
const errors = ref({});
const loading = ref(false);

async function submit() {
    loading.value = true;
    errors.value = {};
    try {
        const { data } = await axios.post('/api/auth/login', form.value);
        auth.user = data.user;
        const next = route.query.next || (data.user?.is_admin ? '/admin' : '/dashboard');
        router.push(String(next));
    } catch (e) {
        errors.value = e.response?.data?.errors ?? { email: ['Алдаа гарлаа. Дахин оролдоно уу.'] };
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div class="mx-auto flex max-w-md flex-col px-4 py-16">
        <div class="rounded-2xl border border-stone-100 bg-white p-8 shadow-sm">
            <div class="text-center">
                <Logo variant="badge" img-class="mx-auto h-16 w-16" />
                <h1 class="mt-3 text-xl font-bold text-pine-900">{{ t('auth.loginTitle') }}</h1>
                <p class="mt-1 text-sm text-stone-500">National Park Academy</p>
            </div>

            <form class="mt-6 grid gap-4" @submit.prevent="submit">
                <div>
                    <label class="label">{{ t('auth.email') }}</label>
                    <input v-model="form.email" type="email" class="input w-full" placeholder="tanii@mail.mn" autocomplete="username" />
                    <p v-if="errors.email" class="err">{{ errors.email[0] }}</p>
                </div>
                <div>
                    <label class="label">{{ t('auth.password') }}</label>
                    <input v-model="form.password" type="password" class="input w-full" autocomplete="current-password" />
                    <p v-if="errors.password" class="err">{{ errors.password[0] }}</p>
                </div>
                <label class="flex items-center gap-2 text-sm text-stone-600">
                    <input v-model="form.remember" type="checkbox" class="h-4 w-4 rounded border-stone-300 accent-pine-700" />
                    {{ t('auth.remember') }}
                </label>
                <button type="submit" class="rounded-xl bg-pine-700 py-2.5 font-semibold text-white transition hover:bg-pine-800" :disabled="loading">
                    {{ loading ? t('common.loading') : t('auth.loginTitle') }}
                </button>
            </form>

            <div class="mt-5 flex items-center justify-between text-sm">
                <router-link to="/forgot" class="font-medium text-pine-600 hover:text-pine-800">{{ t('auth.forgot') }}</router-link>
                <router-link to="/register" class="font-medium text-pine-600 hover:text-pine-800">
                    {{ t('auth.noAccount') }} {{ t('auth.registerTitle') }}
                </router-link>
            </div>
        </div>
    </div>
</template>

<style scoped>
@reference '../../css/app.css';

.label { @apply mb-1 block text-sm font-medium text-stone-600; }
.input { @apply rounded-xl border border-stone-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-pine-400; }
.err { @apply mt-1 text-xs text-red-600; }
</style>
