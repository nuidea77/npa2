<script setup>
import { onMounted, ref } from 'vue';
import axios from '../bootstrap';
import { t } from '../i18n';
import { useAuthStore } from '../stores/auth';
import Modal from '../components/Modal.vue';
import Icon from '../components/Icon.vue';
import RegButton from '../components/RegButton.vue';

const auth = useAuthStore();
const data = ref(null);
const loading = ref(true);

// Тамганы дэлгэрэнгүй
const stampDetail = ref(null);

// Мэдээлэл засах
const showEdit = ref(false);
const editForm = ref({ email: '', position: '', phone: '', emergency_phone: '', current_password: '', new_password: '', new_password_confirmation: '' });
const editErrors = ref({});
const editMsg = ref('');
const saving = ref(false);

// Зураг оруулах
const photoInput = ref(null);

async function load() {
    loading.value = true;
    try {
        const res = await axios.get('/api/dashboard');
        data.value = res.data;
        editForm.value.email = res.data.user.email;
        editForm.value.position = res.data.user.position;
        editForm.value.phone = res.data.user.phone;
        editForm.value.emergency_phone = res.data.user.emergency_phone;
    } finally {
        loading.value = false;
    }
}

onMounted(load);

async function openStamp(id) {
    const { data: d } = await axios.get(`/api/stamps/${id}`);
    stampDetail.value = d.stamp;
}

async function saveEdit() {
    saving.value = true;
    editErrors.value = {};
    try {
        const { data: d } = await axios.put('/api/me', editForm.value);
        editMsg.value = d.message;
        showEdit.value = false;
        editForm.value.current_password = '';
        editForm.value.new_password = '';
        editForm.value.new_password_confirmation = '';
        await load();
        await auth.fetchUser();
    } catch (e) {
        editErrors.value = e.response?.data?.errors ?? {};
    } finally {
        saving.value = false;
    }
}

async function uploadPhoto(e) {
    const file = e.target.files?.[0];
    if (!file) return;
    const fd = new FormData();
    fd.append('photo', file);
    try {
        await axios.post('/api/me/photo', fd);
        await load();
        await auth.fetchUser();
    } catch (err) {
        alert(err.response?.data?.errors?.photo?.[0] ?? 'Зураг оруулахад алдаа гарлаа.');
    }
}

function ytEmbed(url) {
    const m = url?.match(/(?:youtu\.be\/|v=)([\w-]{6,})/);
    return m ? `https://www.youtube.com/embed/${m[1]}` : url;
}
</script>

<template>
    <div class="bg-stone-50">
        <div class="mx-auto max-w-6xl px-4 py-10">
            <h1 class="text-2xl font-bold text-pine-900">{{ t('dash.title') }}</h1>

            <div v-if="loading" class="mt-8 text-stone-400">{{ t('common.loading') }}</div>

            <div v-else-if="data" class="mt-6 grid gap-6 lg:grid-cols-3">
                <!-- Профайл -->
                <section class="rounded-2xl border border-stone-100 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <img v-if="data.user.photo" :src="data.user.photo" class="h-20 w-20 rounded-full object-cover" alt="" />
                            <div v-else class="flex h-20 w-20 items-center justify-center rounded-full bg-pine-100 text-pine-800">
                                <Icon name="user" :size="38" />
                            </div>
                            <button
                                class="absolute -bottom-1 -right-1 flex h-7 w-7 items-center justify-center rounded-full bg-pine-700 text-xs text-white shadow hover:bg-pine-800"
                                title="Зураг оруулах"
                                @click="photoInput.click()"
                            ><Icon name="camera" :size="14" /></button>
                            <input ref="photoInput" type="file" accept="image/*" class="hidden" @change="uploadPhoto" />
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-stone-800">{{ data.user.name }}</h2>
                            <div class="text-sm text-stone-500">{{ data.user.position }}</div>
                        </div>
                    </div>

                    <dl class="mt-5 space-y-2.5 text-sm">
                        <div class="flex justify-between gap-3"><dt class="text-stone-400">{{ t('dash.age') }}</dt><dd class="font-medium text-stone-700">{{ data.user.age ?? '—' }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-stone-400">{{ t('auth.birthDate') }}</dt><dd class="font-medium text-stone-700">{{ data.user.birth_date }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-stone-400">{{ t('auth.org') }}</dt><dd class="text-right font-medium text-stone-700">{{ data.user.org?.name }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-stone-400">{{ t('auth.phone') }}</dt><dd class="font-medium text-stone-700">{{ data.user.phone }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-stone-400">{{ t('auth.emergencyPhone') }}</dt><dd class="font-medium text-stone-700">{{ data.user.emergency_phone ?? '—' }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-stone-400">{{ t('auth.email') }}</dt><dd class="font-medium text-stone-700">{{ data.user.email }}</dd></div>
                    </dl>

                    <button class="mt-5 w-full rounded-full border border-pine-200 py-2 text-sm font-semibold text-pine-700 transition hover:bg-pine-50" @click="showEdit = true">
                        {{ t('dash.editProfile') }}
                    </button>
                </section>

                <!-- NPA тамга -->
                <section class="rounded-2xl border border-stone-100 bg-white p-6 shadow-sm lg:col-span-2">
                    <div class="flex items-center justify-between">
                        <h2 class="flex items-center gap-2 text-lg font-bold text-pine-900"><Icon name="award" :size="21" class="text-pine-700" /> {{ t('dash.stamps') }}</h2>
                        <div class="flex gap-4 text-sm">
                            <div><span class="font-bold text-pine-700">{{ data.stamps.current_year }}</span> <span class="text-stone-400">{{ t('dash.stampsYear') }}</span></div>
                            <div><span class="font-bold text-pine-700">{{ data.stamps.total }}</span> <span class="text-stone-400">{{ t('dash.stampsTotal') }}</span></div>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-stone-400">{{ t('dash.stampsMax') }} ({{ data.user.org?.name }})</p>

                    <div v-if="!data.stamps.by_year.length" class="mt-5 rounded-xl bg-stone-50 p-5 text-sm text-stone-500">{{ t('common.empty') }}</div>

                    <!-- Жил жилээр цуглуулсан түүх -->
                    <div v-for="y in data.stamps.by_year" :key="y.year" class="mt-5">
                        <div class="flex items-center gap-3">
                            <img v-if="y.design_image" :src="y.design_image" class="h-10 w-10" alt="" />
                            <h3 class="font-bold text-stone-800">NPA тамга {{ y.year }}</h3>
                            <span class="rounded-full bg-pine-100 px-2.5 py-0.5 text-xs font-bold text-pine-800">{{ y.count }}</span>
                            <span v-if="y.start_date" class="text-xs text-stone-400">{{ y.start_date }} — {{ y.end_date }}</span>
                        </div>
                        <div class="mt-2 grid gap-2">
                            <button
                                v-for="s in y.stamps"
                                :key="s.id"
                                class="flex items-center justify-between gap-3 rounded-xl border border-stone-100 bg-stone-50 px-4 py-2.5 text-left text-sm transition hover:border-pine-200 hover:bg-pine-50"
                                @click="openStamp(s.id)"
                            >
                                <span class="font-medium text-stone-700">{{ s.name }}</span>
                                <span class="shrink-0 text-xs text-stone-400">{{ s.stamp_date }}</span>
                            </button>
                        </div>
                    </div>
                </section>

                <!-- Сургалт -->
                <section class="rounded-2xl border border-stone-100 bg-white p-6 shadow-sm lg:col-span-2">
                    <h2 class="flex items-center gap-2 text-lg font-bold text-pine-900"><Icon name="graduation" :size="21" class="text-pine-700" /> {{ t('dash.trainings') }}</h2>
                    <div v-if="!data.trainings.length" class="mt-4 rounded-xl bg-stone-50 p-5 text-sm text-stone-500">{{ t('common.empty') }}</div>
                    <div v-else class="mt-4 grid gap-4 md:grid-cols-2">
                        <div v-for="tr in data.trainings" :key="tr.id" class="overflow-hidden rounded-xl border border-stone-100">
                            <div v-if="tr.type === 'youtube'" class="aspect-video">
                                <iframe class="h-full w-full" :src="ytEmbed(tr.url)" :title="tr.title" frameborder="0" allowfullscreen></iframe>
                            </div>
                            <img v-else-if="tr.type === 'image'" :src="tr.url" :alt="tr.title" class="h-40 w-full object-cover" />
                            <a v-else :href="tr.url" target="_blank" class="flex h-28 items-center justify-center bg-sand-100 text-pine-800"><Icon name="file-text" :size="40" /></a>
                            <div class="p-3">
                                <h3 class="text-sm font-bold text-stone-800">{{ tr.title }}</h3>
                                <p class="mt-1 text-xs text-stone-500">{{ tr.summary }}</p>
                                <div class="mt-1.5 text-xs text-stone-400">{{ tr.published_at }}</div>
                                <a v-if="tr.type === 'pdf'" :href="tr.url" target="_blank" class="mt-1 inline-block text-xs font-semibold text-pine-600">PDF нээх →</a>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Мэдээлэл: удахгүй болох арга хэмжээ + миний бүртгэлүүд -->
                <section class="space-y-6">
                    <div class="rounded-2xl border border-stone-100 bg-white p-6 shadow-sm">
                        <h2 class="flex items-center gap-2 text-lg font-bold text-pine-900"><Icon name="calendar" :size="21" class="text-pine-700" /> {{ t('dash.events') }}</h2>
                        <div v-if="!data.events.length" class="mt-4 rounded-xl bg-stone-50 p-4 text-sm text-stone-500">{{ t('common.empty') }}</div>
                        <div v-for="e in data.events" :key="e.id" class="mt-4 rounded-xl border border-stone-100 p-4">
                            <h3 class="text-sm font-bold text-stone-800">{{ e.title }}</h3>
                            <p class="mt-1 text-xs text-stone-500">{{ e.description }}</p>
                            <div class="mt-3"><RegButton :event="e" /></div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-stone-100 bg-white p-6 shadow-sm">
                        <h2 class="flex items-center gap-2 text-lg font-bold text-pine-900"><Icon name="edit" :size="21" class="text-pine-700" /> {{ t('dash.myRegs') }}</h2>
                        <div v-if="!data.my_registrations.length" class="mt-4 rounded-xl bg-stone-50 p-4 text-sm text-stone-500">{{ t('common.empty') }}</div>
                        <ul v-else class="mt-3 space-y-2 text-sm">
                            <li v-for="r in data.my_registrations" :key="r.id" class="flex justify-between gap-2 rounded-xl bg-stone-50 px-3 py-2">
                                <span class="font-medium text-stone-700">{{ r.event }}</span>
                                <span class="shrink-0 text-xs text-stone-400">{{ r.registered_at }}</span>
                            </li>
                        </ul>
                    </div>

                    <router-link to="/help#feedback" class="block rounded-2xl border-2 border-pine-600 p-5 text-center font-bold text-pine-700 transition hover:bg-pine-50">
                        {{ t('dash.feedback') }}
                    </router-link>
                </section>
            </div>
        </div>

        <!-- Тамганы дэлгэрэнгүй -->
        <Modal :show="!!stampDetail" :title="stampDetail?.name" @close="stampDetail = null">
            <div v-if="stampDetail" class="space-y-2 text-sm text-stone-700">
                <div><span class="text-stone-400">ХЗ:</span> {{ stampDetail.org?.name }}</div>
                <div><span class="text-stone-400">Тамга даруулсан огноо:</span> {{ stampDetail.stamp_date?.slice(0, 10) }}</div>
                <div v-if="stampDetail.staff_name"><span class="text-stone-400">Тамга авсан ажилтан:</span> {{ stampDetail.staff_name }} ({{ stampDetail.staff_position }})</div>
                <div v-if="stampDetail.note"><span class="text-stone-400">Тайлбар:</span> {{ stampDetail.note }}</div>
                <div><span class="text-stone-400">{{ t('common.status') }}:</span> {{ { active: 'Идэвхтэй', edited: 'Засварласан', deleted: 'Устгасан' }[stampDetail.status] }}</div>
            </div>
        </Modal>

        <!-- Мэдээлэл засах -->
        <Modal :show="showEdit" :title="t('dash.editProfile')" @close="showEdit = false">
            <div class="grid gap-3">
                <div>
                    <label class="label">{{ t('auth.email') }}</label>
                    <input v-model="editForm.email" type="email" class="input w-full" />
                    <p v-if="editErrors.email" class="err">{{ editErrors.email[0] }}</p>
                </div>
                <div>
                    <label class="label">{{ t('auth.position') }}</label>
                    <select v-model="editForm.position" class="input w-full">
                        <option v-for="p in (auth.settings?.positions ?? [])" :key="p" :value="p">{{ p }}</option>
                    </select>
                    <p v-if="editErrors.position" class="err">{{ editErrors.position[0] }}</p>
                </div>
                <div>
                    <label class="label">{{ t('auth.phone') }}</label>
                    <input v-model="editForm.phone" class="input w-full" />
                    <p v-if="editErrors.phone" class="err">{{ editErrors.phone[0] }}</p>
                </div>
                <div>
                    <label class="label">{{ t('auth.emergencyPhone') }}</label>
                    <input v-model="editForm.emergency_phone" class="input w-full" />
                </div>
                <div class="grid gap-3 border-t border-stone-100 pt-3 sm:grid-cols-2">
                    <div>
                        <label class="label">Шинэ нууц үг <span class="text-xs text-stone-400">(солих бол)</span></label>
                        <input v-model="editForm.new_password" type="password" class="input w-full" autocomplete="new-password" />
                        <p v-if="editErrors.new_password" class="err">{{ editErrors.new_password[0] }}</p>
                    </div>
                    <div>
                        <label class="label">Шинэ нууц үг давтах</label>
                        <input v-model="editForm.new_password_confirmation" type="password" class="input w-full" autocomplete="new-password" />
                    </div>
                </div>
                <div class="border-t border-stone-100 pt-3">
                    <label class="label">Одоогийн нууц үг * <span class="text-xs text-stone-400">(баталгаажуулахад)</span></label>
                    <input v-model="editForm.current_password" type="password" class="input w-full" autocomplete="current-password" />
                    <p v-if="editErrors.current_password" class="err">{{ editErrors.current_password[0] }}</p>
                </div>
            </div>
            <template #actions>
                <button class="mr-2 rounded-full px-4 py-2 text-sm text-stone-500 hover:bg-stone-100" @click="showEdit = false">{{ t('common.cancel') }}</button>
                <button class="rounded-full bg-pine-700 px-5 py-2 text-sm font-semibold text-white hover:bg-pine-800" :disabled="saving" @click="saveEdit">
                    {{ saving ? t('common.loading') : t('common.save') }}
                </button>
            </template>
        </Modal>

        <!-- Амжилтын мэдэгдэл -->
        <Modal :show="!!editMsg" @close="editMsg = ''">
            <div class="text-center">
                <div class="text-4xl">✅</div>
                <p class="mt-3 font-medium text-stone-700">{{ editMsg }}</p>
            </div>
        </Modal>
    </div>
</template>

<style scoped>
@reference '../../css/app.css';

.label { @apply mb-1 block text-sm font-medium text-stone-600; }
.input { @apply rounded-xl border border-stone-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-pine-400; }
.err { @apply mt-1 text-xs text-red-600; }
</style>
