<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from '../bootstrap';
import { t } from '../i18n';
import { useAuthStore } from '../stores/auth';
import Modal from '../components/Modal.vue';
import Icon from '../components/Icon.vue';

const auth = useAuthStore();
const router = useRouter();

const orgs = ref([]);
const positions = computed(() => auth.settings?.positions ?? []);

const form = ref({
    last_name: '', first_name: '', birth_date: '', gender: '',
    org_id: '', position: '', phone: '', emergency_phone: '',
    email: '', password: '', password_confirmation: '',
});
const errors = ref({});
const loading = ref(false);
const successMsg = ref('');
const successNote = ref('');

onMounted(async () => {
    const { data } = await axios.get('/api/orgs');
    orgs.value = data.orgs;
});

async function submit() {
    loading.value = true;
    errors.value = {};
    try {
        const { data } = await axios.post('/api/auth/register', form.value);
        successMsg.value = data.message;
        successNote.value = data.note;
    } catch (e) {
        errors.value = e.response?.data?.errors ?? {};
    } finally {
        loading.value = false;
    }
}

function closeSuccess() {
    successMsg.value = '';
    router.push('/');
}
</script>

<template>
    <div class="mx-auto max-w-2xl px-4 py-12">
        <div class="rounded-2xl border border-stone-100 bg-white p-8 shadow-sm">
            <h1 class="text-xl font-bold text-pine-900">{{ t('auth.registerTitle') }}</h1>
            <p class="mt-2 rounded-xl bg-pine-50 p-3 text-sm text-pine-800">{{ t('auth.registerNote') }}</p>

            <form class="mt-6 grid gap-4" @submit.prevent="submit">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label">{{ t('auth.lastName') }} *</label>
                        <input v-model="form.last_name" class="input w-full" placeholder="Кириллээр" />
                        <p v-if="errors.last_name" class="err">{{ errors.last_name[0] }}</p>
                    </div>
                    <div>
                        <label class="label">{{ t('auth.firstName') }} *</label>
                        <input v-model="form.first_name" class="input w-full" placeholder="Кириллээр" />
                        <p v-if="errors.first_name" class="err">{{ errors.first_name[0] }}</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label">{{ t('auth.birthDate') }} *</label>
                        <input v-model="form.birth_date" type="date" class="input w-full" />
                        <p v-if="errors.birth_date" class="err">{{ errors.birth_date[0] }}</p>
                    </div>
                    <div>
                        <label class="label">{{ t('auth.gender') }} *</label>
                        <div class="flex gap-3 pt-1.5">
                            <label class="flex items-center gap-2 text-sm">
                                <input v-model="form.gender" type="radio" value="male" class="accent-pine-700" /> {{ t('auth.male') }}
                            </label>
                            <label class="flex items-center gap-2 text-sm">
                                <input v-model="form.gender" type="radio" value="female" class="accent-pine-700" /> {{ t('auth.female') }}
                            </label>
                        </div>
                        <p v-if="errors.gender" class="err">{{ errors.gender[0] }}</p>
                    </div>
                </div>

                <div>
                    <label class="label">{{ t('auth.org') }} *</label>
                    <select v-model="form.org_id" class="input w-full">
                        <option value="" disabled>{{ t('common.select') }}</option>
                        <option v-for="o in orgs" :key="o.id" :value="o.id">{{ o.name }}</option>
                    </select>
                    <p v-if="errors.org_id" class="err">{{ errors.org_id[0] }}</p>
                </div>

                <div>
                    <label class="label">{{ t('auth.position') }} *</label>
                    <select v-model="form.position" class="input w-full">
                        <option value="" disabled>{{ t('common.select') }}</option>
                        <option v-for="p in positions" :key="p" :value="p">{{ p }}</option>
                    </select>
                    <p v-if="errors.position" class="err">{{ errors.position[0] }}</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label">{{ t('auth.phone') }} *</label>
                        <input v-model="form.phone" class="input w-full" placeholder="99112233" />
                        <p v-if="errors.phone" class="err">{{ errors.phone[0] }}</p>
                    </div>
                    <div>
                        <label class="label">{{ t('auth.emergencyPhone') }}</label>
                        <input v-model="form.emergency_phone" class="input w-full" placeholder="99887766" />
                        <p v-if="errors.emergency_phone" class="err">{{ errors.emergency_phone[0] }}</p>
                    </div>
                </div>

                <div>
                    <label class="label">{{ t('auth.email') }} * <span class="text-xs text-stone-400">(нэвтрэх нэр болно)</span></label>
                    <input v-model="form.email" type="email" class="input w-full" placeholder="tanii@mail.mn" />
                    <p v-if="errors.email" class="err">{{ errors.email[0] }}</p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label">{{ t('auth.password') }} *</label>
                        <input v-model="form.password" type="password" class="input w-full" autocomplete="new-password" />
                        <p v-if="errors.password" class="err">{{ errors.password[0] }}</p>
                    </div>
                    <div>
                        <label class="label">{{ t('auth.passwordConfirm') }} *</label>
                        <input v-model="form.password_confirmation" type="password" class="input w-full" autocomplete="new-password" />
                    </div>
                </div>

                <button class="mt-2 rounded-xl bg-pine-700 py-3 font-bold text-white transition hover:bg-pine-800" :disabled="loading">
                    {{ loading ? t('common.loading') : t('auth.finish') }}
                </button>

                <div class="text-center text-sm">
                    <router-link to="/login" class="font-medium text-pine-600 hover:text-pine-800">
                        {{ t('auth.haveAccount') }} {{ t('auth.loginTitle') }}
                    </router-link>
                </div>
            </form>
        </div>

        <!-- Амжилттай илгээгдсэн popup -->
        <Modal :show="!!successMsg" @close="closeSuccess">
            <div class="text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-pine-100 text-pine-700"><Icon name="check" :size="30" /></div>
                <p class="mt-3 font-medium text-stone-700">{{ successMsg }}</p>
                <p class="mt-2 text-sm text-stone-500">{{ successNote }}</p>
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

.label { @apply mb-1 block text-sm font-medium text-stone-600; }
.input { @apply rounded-xl border border-stone-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-pine-400; }
.err { @apply mt-1 text-xs text-red-600; }
</style>
