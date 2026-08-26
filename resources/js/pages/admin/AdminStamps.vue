<script setup>
import { onMounted, ref } from 'vue';
import axios from '../../bootstrap';
import Modal from '../../components/Modal.vue';

const stamps = ref([]);
const summary = ref([]);
const years = ref([]);
const orgs = ref([]);
const loading = ref(true);
const filters = ref({ org_id: '', year: '', with_deleted: false });

const statusLabels = { active: 'Идэвхтэй', edited: 'Засварласан', deleted: 'Устгасан' };
const statusClasses = {
    active: 'bg-pine-100 text-pine-800',
    edited: 'bg-amber-100 text-amber-800',
    deleted: 'bg-red-100 text-red-700',
};

const form = ref(null);      // тамга нэмэх/засах
const errors = ref({});
const deleting = ref(null);  // хасах гэж буй тамга
const deleteReason = ref('');
const detail = ref(null);    // дэлгэрэнгүй + лог
const yearForm = ref(null);  // жилийн тохиргоо

async function load() {
    loading.value = true;
    try {
        const params = { ...filters.value, with_deleted: filters.value.with_deleted ? 1 : 0 };
        const [s, o] = await Promise.all([
            axios.get('/api/admin/stamps', { params }),
            axios.get('/api/orgs'),
        ]);
        stamps.value = s.data.stamps;
        summary.value = s.data.summary;
        years.value = s.data.years;
        orgs.value = o.data.orgs;
    } finally {
        loading.value = false;
    }
}

onMounted(load);

function newStamp() {
    form.value = { org_id: '', name: '', staff_name: '', staff_position: '', stamp_date: new Date().toISOString().slice(0, 10), note: '' };
    errors.value = {};
}

function editStamp(s) {
    form.value = { id: s.id, org_id: s.org_id, name: s.name, staff_name: s.staff_name, staff_position: s.staff_position, stamp_date: s.stamp_date?.slice(0, 10), note: s.note };
    errors.value = {};
}

async function saveStamp() {
    errors.value = {};
    try {
        if (form.value.id) {
            await axios.put(`/api/admin/stamps/${form.value.id}`, form.value);
        } else {
            await axios.post('/api/admin/stamps', form.value);
        }
        form.value = null;
        await load();
    } catch (e) {
        errors.value = e.response?.data?.errors ?? {};
        if (e.response?.data?.message && !Object.keys(errors.value).length) {
            alert(e.response.data.message);
        }
    }
}

async function confirmDelete() {
    try {
        await axios.post(`/api/admin/stamps/${deleting.value.id}/delete`, { reason: deleteReason.value });
        deleting.value = null;
        deleteReason.value = '';
        await load();
    } catch (e) {
        alert(e.response?.data?.errors?.reason?.[0] ?? 'Алдаа гарлаа.');
    }
}

async function openDetail(s) {
    const { data } = await axios.get(`/api/admin/stamps/${s.id}`);
    detail.value = data.stamp;
}

async function saveYear() {
    try {
        await axios.post('/api/admin/stamp-years', yearForm.value);
        yearForm.value = null;
        await load();
    } catch (e) {
        alert(Object.values(e.response?.data?.errors ?? {}).flat().join('\n') || 'Алдаа гарлаа.');
    }
}
</script>

<template>
    <div>
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-pine-900">NPA тамга удирдах</h1>
                <p class="mt-1 text-sm text-stone-500">Тамга нь календарын жилээр шинэчлэгдэнэ (reset). Жилд ХЗ тус бүр хамгийн ихдээ 50 тамга.</p>
            </div>
            <button class="btn-primary" @click="newStamp">+ ХЗ тамга нэмэх</button>
        </div>

        <!-- Жилийн тохиргоо -->
        <div class="mt-5 rounded-2xl border border-stone-100 bg-white p-4 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-sm font-bold text-stone-700">Жил бүрийн тамганы тохиргоо</h2>
                <button class="text-xs font-semibold text-pine-600 hover:underline" @click="yearForm = { year: new Date().getFullYear() + 1, start_date: '', end_date: '', design_image_url: '' }">+ Жил нэмэх</button>
            </div>
            <div class="mt-3 flex flex-wrap gap-3">
                <div v-for="y in years" :key="y.id" class="flex items-center gap-2.5 rounded-xl border border-stone-100 bg-stone-50 px-3 py-2">
                    <img v-if="y.design_image" :src="y.design_image" class="h-9 w-9" alt="" />
                    <div>
                        <div class="text-sm font-bold text-stone-700">NPA тамга {{ y.year }}</div>
                        <div class="text-xs text-stone-400">{{ y.start_date }} — {{ y.end_date }}</div>
                    </div>
                    <button class="text-xs text-pine-600 hover:underline" @click="yearForm = { year: y.year, start_date: y.start_date, end_date: y.end_date, design_image_url: y.design_image }">Засах</button>
                </div>
            </div>
        </div>

        <!-- Нийт тамга цуглуулсан ХЗ-д -->
        <div class="mt-5 rounded-2xl border border-stone-100 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-bold text-stone-700">Нийт тамга цуглуулалт (ХЗ-ээр)</h2>
            <div class="mt-3 grid gap-1.5 sm:grid-cols-2 lg:grid-cols-3">
                <button
                    v-for="s in summary.filter((x) => x.stamps_count > 0)"
                    :key="s.id"
                    class="flex items-center justify-between rounded-xl bg-stone-50 px-3 py-2 text-left text-sm transition hover:bg-pine-50"
                    @click="filters.org_id = s.id; load()"
                >
                    <span class="truncate text-stone-700">{{ s.name }}</span>
                    <span class="ml-2 shrink-0 rounded-full bg-pine-100 px-2 py-0.5 text-xs font-bold text-pine-800">{{ s.stamps_count }}</span>
                </button>
            </div>
        </div>

        <!-- Шүүлтүүр -->
        <div class="mt-5 flex flex-wrap gap-2">
            <select v-model="filters.org_id" class="input" @change="load">
                <option value="">Бүх ХЗ</option>
                <option v-for="o in orgs" :key="o.id" :value="o.id">{{ o.name }}</option>
            </select>
            <select v-model="filters.year" class="input" @change="load">
                <option value="">Бүх жил</option>
                <option v-for="y in years" :key="y.id" :value="y.year">{{ y.year }}</option>
            </select>
            <label class="flex items-center gap-2 rounded-xl border border-stone-200 bg-white px-3 text-sm text-stone-600">
                <input v-model="filters.with_deleted" type="checkbox" class="accent-pine-700" @change="load" /> Устгасныг харуулах
            </label>
        </div>

        <!-- Тамганы жагсаалт -->
        <div v-if="loading" class="mt-6 text-stone-400">Ачаалж байна...</div>
        <div v-else class="mt-4 overflow-x-auto rounded-2xl border border-stone-100 bg-white shadow-sm">
            <table class="w-full min-w-[800px] text-sm">
                <thead class="bg-stone-50 text-left text-xs uppercase tracking-wide text-stone-400">
                    <tr>
                        <th class="px-4 py-3">ХЗ</th>
                        <th class="px-4 py-3">Тамганы нэр</th>
                        <th class="px-4 py-3">Ажилтан</th>
                        <th class="px-4 py-3">Огноо</th>
                        <th class="px-4 py-3">Статус</th>
                        <th class="px-4 py-3 text-right">Үйлдэл</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-50">
                    <tr v-for="s in stamps" :key="s.id" :class="{ 'opacity-50': s.status === 'deleted' }">
                        <td class="max-w-52 truncate px-4 py-3">{{ s.org?.name }}</td>
                        <td class="px-4 py-3 font-medium text-stone-700">{{ s.name }}</td>
                        <td class="px-4 py-3">{{ s.staff_name }}<div class="text-xs text-stone-400">{{ s.staff_position }}</div></td>
                        <td class="px-4 py-3">{{ s.stamp_date?.slice(0, 10) }} <div class="text-xs text-stone-400">{{ s.year }}</div></td>
                        <td class="px-4 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-bold" :class="statusClasses[s.status]">{{ statusLabels[s.status] }}</span></td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-1.5">
                                <button class="act bg-stone-100 text-stone-600 hover:bg-stone-200" @click="openDetail(s)">Лог</button>
                                <template v-if="s.status !== 'deleted'">
                                    <button class="act bg-stone-100 text-stone-600 hover:bg-stone-200" @click="editStamp(s)">Засах</button>
                                    <button class="act bg-red-50 text-red-600 hover:bg-red-100" @click="deleting = s">Хасах</button>
                                </template>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Тамга нэмэх/засах -->
        <Modal :show="!!form" :title="form?.id ? 'Тамга засварлах' : 'ХЗ тамга нэмэх'" @close="form = null">
            <div v-if="form" class="grid gap-3">
                <div>
                    <select v-model="form.org_id" class="input w-full">
                        <option value="" disabled>ХЗ сонгох *</option>
                        <option v-for="o in orgs" :key="o.id" :value="o.id">{{ o.name }}</option>
                    </select>
                    <p v-if="errors.org_id" class="err">{{ errors.org_id[0] }}</p>
                </div>
                <div>
                    <input v-model="form.name" class="input w-full" placeholder="Тамга авсан нэр (ж: Бүсийн сургалтад хамрагдав) *" />
                    <p v-if="errors.name" class="err">{{ errors.name[0] }}</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <input v-model="form.staff_name" class="input" placeholder="Ажилтны нэр" />
                    <input v-model="form.staff_position" class="input" placeholder="Албан тушаал" />
                </div>
                <div>
                    <input v-model="form.stamp_date" type="date" class="input w-full" />
                    <p v-if="errors.stamp_date" class="err">{{ errors.stamp_date[0] }}</p>
                </div>
                <textarea v-model="form.note" rows="2" class="input w-full" placeholder="Нэмэлт тайлбар"></textarea>
            </div>
            <template #actions>
                <button class="mr-2 rounded-full px-4 py-2 text-sm text-stone-500 hover:bg-stone-100" @click="form = null">Болих</button>
                <button class="btn-primary" @click="saveStamp">Хадгалах</button>
            </template>
        </Modal>

        <!-- Тамга хасах -->
        <Modal :show="!!deleting" title="ХЗ тамга хасах" @close="deleting = null">
            <p class="text-sm text-stone-600">«{{ deleting?.name }}» тамгыг хасахад тамганы тоо автоматаар шинэчлэгдэж, админы лог үлдэнэ.</p>
            <textarea v-model="deleteReason" rows="3" class="input mt-3 w-full" placeholder="Хасах шалтгаан *"></textarea>
            <template #actions>
                <button class="mr-2 rounded-full px-4 py-2 text-sm text-stone-500 hover:bg-stone-100" @click="deleting = null">Болих</button>
                <button class="rounded-full bg-red-600 px-5 py-2 text-sm font-semibold text-white hover:bg-red-700" @click="confirmDelete">Хасах</button>
            </template>
        </Modal>

        <!-- Дэлгэрэнгүй + админы лог -->
        <Modal :show="!!detail" :title="detail?.name" @close="detail = null">
            <div v-if="detail" class="space-y-2 text-sm text-stone-700">
                <div><span class="text-stone-400">ХЗ:</span> {{ detail.org?.name }}</div>
                <div><span class="text-stone-400">Огноо:</span> {{ detail.stamp_date?.slice(0, 10) }}</div>
                <div v-if="detail.staff_name"><span class="text-stone-400">Ажилтан:</span> {{ detail.staff_name }} ({{ detail.staff_position }})</div>
                <div v-if="detail.note"><span class="text-stone-400">Тайлбар:</span> {{ detail.note }}</div>
                <div v-if="detail.delete_reason"><span class="text-stone-400">Хассан шалтгаан:</span> {{ detail.delete_reason }}</div>
                <div class="border-t border-stone-100 pt-2">
                    <div class="mb-1.5 text-xs font-bold uppercase tracking-wide text-stone-400">Админы лог</div>
                    <div v-for="l in detail.logs" :key="l.id" class="mb-1.5 rounded-lg bg-stone-50 px-3 py-2 text-xs">
                        <div class="flex justify-between gap-2">
                            <span class="font-semibold">{{ { created: '➕ Нэмсэн', edited: '✏️ Зассан', deleted: '🗑️ Хассан' }[l.action] ?? l.action }}</span>
                            <span class="text-stone-400">{{ l.created_at?.slice(0, 16).replace('T', ' ') }}</span>
                        </div>
                        <div class="text-stone-500">{{ l.admin_name }} — {{ l.note }}</div>
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Жилийн тохиргоо -->
        <Modal :show="!!yearForm" title="Жилийн тамганы тохиргоо" @close="yearForm = null">
            <div v-if="yearForm" class="grid gap-3">
                <input v-model.number="yearForm.year" type="number" class="input w-full" placeholder="Жил" />
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="text-xs text-stone-400">Эхлэх огноо</label><input v-model="yearForm.start_date" type="date" class="input w-full" /></div>
                    <div><label class="text-xs text-stone-400">Дуусах огноо</label><input v-model="yearForm.end_date" type="date" class="input w-full" /></div>
                </div>
                <input v-model="yearForm.design_image_url" class="input w-full" placeholder="Загвар зургийн зам (ж: /images/stamp-2027.svg)" />
            </div>
            <template #actions>
                <button class="mr-2 rounded-full px-4 py-2 text-sm text-stone-500 hover:bg-stone-100" @click="yearForm = null">Болих</button>
                <button class="btn-primary" @click="saveYear">Хадгалах</button>
            </template>
        </Modal>
    </div>
</template>

<style scoped>
@reference '../../../css/app.css';

.input { @apply rounded-xl border border-stone-200 bg-white px-3.5 py-2 text-sm outline-none transition focus:border-pine-400; }
.btn-primary { @apply rounded-full bg-pine-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-pine-800; }
.act { @apply rounded-full px-2.5 py-1 text-xs font-semibold transition; }
.err { @apply mt-1 text-xs text-red-600; }
</style>
