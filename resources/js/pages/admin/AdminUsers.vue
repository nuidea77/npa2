<script setup>
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import axios from '../../bootstrap';
import { useAuthStore } from '../../stores/auth';
import Modal from '../../components/Modal.vue';

const auth = useAuthStore();
const route = useRoute();

const users = ref([]);
const orgs = ref([]);
const loading = ref(true);
const filters = ref({ status: String(route.query.status ?? ''), q: '' });

const statusLabels = { pending: 'Хүлээгдэж буй', active: 'Идэвхтэй', rejected: 'Татгалзсан' };
const statusClasses = {
    pending: 'bg-amber-100 text-amber-800',
    active: 'bg-pine-100 text-pine-800',
    rejected: 'bg-red-100 text-red-700',
};

const editUser = ref(null);
const tempPassword = ref('');

async function load() {
    loading.value = true;
    try {
        const [u, o] = await Promise.all([
            axios.get('/api/admin/users', { params: filters.value }),
            axios.get('/api/orgs'),
        ]);
        users.value = u.data.users;
        orgs.value = o.data.orgs;
    } finally {
        loading.value = false;
    }
}

onMounted(load);

async function approve(u) {
    await axios.post(`/api/admin/users/${u.id}/approve`);
    await load();
}

async function reject(u) {
    if (!confirm(`${u.name} — бүртгэлээс татгалзах уу?`)) return;
    await axios.post(`/api/admin/users/${u.id}/reject`);
    await load();
}

async function destroy(u) {
    if (!confirm(`${u.name} — хэрэглэгчийг бүр мөсөн устгах уу?`)) return;
    await axios.delete(`/api/admin/users/${u.id}`);
    await load();
}

async function resetPassword(u) {
    if (!confirm(`${u.name} — нууц үг сэргээх үү?`)) return;
    const { data } = await axios.post(`/api/admin/users/${u.id}/reset-password`);
    tempPassword.value = data.temp_password;
}

async function saveEdit() {
    const u = editUser.value;
    await axios.put(`/api/admin/users/${u.id}`, {
        last_name: u.last_name, first_name: u.first_name, birth_date: u.birth_date,
        gender: u.gender, org_id: u.org_id, position: u.position,
        phone: u.phone, emergency_phone: u.emergency_phone, email: u.email, status: u.status,
    });
    editUser.value = null;
    await load();
}

async function setRole(u, role) {
    await axios.post(`/api/admin/users/${u.id}/role`, { role });
    await load();
}
</script>

<template>
    <div>
        <h1 class="text-xl font-bold text-pine-900">Хэрэглэгчийн жагсаалт</h1>
        <p class="mt-1 text-sm text-stone-500">Шинэ бүртгэлийг тулган шалгаж баталгаажуулна. Баталгаажсан хэрэглэгч нэвтрэх боломжтой болно.</p>

        <div class="mt-4 flex flex-wrap gap-2">
            <select v-model="filters.status" class="input" @change="load">
                <option value="">Бүх статус</option>
                <option value="pending">Хүлээгдэж буй</option>
                <option value="active">Идэвхтэй</option>
                <option value="rejected">Татгалзсан</option>
            </select>
            <input v-model="filters.q" class="input flex-1" placeholder="Нэр, и-мэйлээр хайх..." @keyup.enter="load" />
            <button class="btn-primary" @click="load">Хайх</button>
        </div>

        <div v-if="loading" class="mt-6 text-stone-400">Ачаалж байна...</div>

        <div v-else class="mt-5 overflow-x-auto rounded-2xl border border-stone-100 bg-white shadow-sm">
            <table class="w-full min-w-[900px] text-sm">
                <thead class="bg-stone-50 text-left text-xs uppercase tracking-wide text-stone-400">
                    <tr>
                        <th class="px-4 py-3">Нэр</th>
                        <th class="px-4 py-3">И-мэйл / Утас</th>
                        <th class="px-4 py-3">ХЗ / Албан тушаал</th>
                        <th class="px-4 py-3">Эрх</th>
                        <th class="px-4 py-3">Статус</th>
                        <th class="px-4 py-3 text-right">Үйлдэл</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-50">
                    <tr v-for="u in users" :key="u.id">
                        <td class="px-4 py-3">
                            <div class="font-medium text-stone-800">{{ u.name }}</div>
                            <div class="text-xs text-stone-400">{{ u.birth_date }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div>{{ u.email }}</div>
                            <div class="text-xs text-stone-400">{{ u.phone }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="max-w-52 truncate">{{ u.org?.name ?? '—' }}</div>
                            <div class="text-xs text-stone-400">{{ u.position }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <select
                                v-if="auth.user?.role === 'superadmin' && u.id !== auth.user.id"
                                :value="u.role"
                                class="rounded-lg border border-stone-200 px-2 py-1 text-xs"
                                @change="setRole(u, $event.target.value)"
                            >
                                <option value="user">Хэрэглэгч</option>
                                <option value="admin">Админ</option>
                                <option value="superadmin">Супер админ</option>
                            </select>
                            <span v-else class="text-xs text-stone-500">{{ { user: 'Хэрэглэгч', admin: 'Админ', superadmin: 'Супер админ' }[u.role] }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-bold" :class="statusClasses[u.status]">{{ statusLabels[u.status] }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-1.5">
                                <button v-if="u.status === 'pending'" class="act bg-pine-700 text-white hover:bg-pine-800" @click="approve(u)">Баталгаажуулах</button>
                                <button v-if="u.status === 'pending'" class="act bg-red-100 text-red-700 hover:bg-red-200" @click="reject(u)">Татгалзах</button>
                                <button class="act bg-stone-100 text-stone-600 hover:bg-stone-200" @click="editUser = { ...u }">Засах</button>
                                <button class="act bg-sand-100 text-sand-800 hover:bg-sand-200" @click="resetPassword(u)">Нууц үг</button>
                                <button class="act bg-red-50 text-red-600 hover:bg-red-100" @click="destroy(u)">Устгах</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Хэрэглэгч засах -->
        <Modal :show="!!editUser" title="Хэрэглэгчийн мэдээлэл засах" @close="editUser = null">
            <div v-if="editUser" class="grid gap-3">
                <div class="grid grid-cols-2 gap-3">
                    <input v-model="editUser.last_name" class="input" placeholder="Овог" />
                    <input v-model="editUser.first_name" class="input" placeholder="Нэр" />
                </div>
                <input v-model="editUser.email" type="email" class="input" placeholder="И-мэйл" />
                <select v-model="editUser.org_id" class="input">
                    <option :value="null">ХЗ сонгоогүй</option>
                    <option v-for="o in orgs" :key="o.id" :value="o.id">{{ o.name }}</option>
                </select>
                <select v-model="editUser.position" class="input">
                    <option v-for="p in (auth.settings?.positions ?? [])" :key="p" :value="p">{{ p }}</option>
                </select>
                <div class="grid grid-cols-2 gap-3">
                    <input v-model="editUser.phone" class="input" placeholder="Утас" />
                    <input v-model="editUser.emergency_phone" class="input" placeholder="Яаралтай утас" />
                </div>
                <select v-model="editUser.status" class="input">
                    <option value="pending">Хүлээгдэж буй</option>
                    <option value="active">Идэвхтэй</option>
                    <option value="rejected">Татгалзсан</option>
                </select>
            </div>
            <template #actions>
                <button class="mr-2 rounded-full px-4 py-2 text-sm text-stone-500 hover:bg-stone-100" @click="editUser = null">Болих</button>
                <button class="btn-primary" @click="saveEdit">Хадгалах</button>
            </template>
        </Modal>

        <!-- Түр нууц үг -->
        <Modal :show="!!tempPassword" title="Түр нууц үг үүсгэгдлээ" @close="tempPassword = ''">
            <p class="text-sm text-stone-600">Хэрэглэгчид дараах түр нууц үгийг дамжуулна уу (и-мэйл логт мөн бүртгэгдсэн):</p>
            <div class="mt-3 rounded-xl bg-stone-100 p-4 text-center font-mono text-lg font-bold tracking-wider">{{ tempPassword }}</div>
        </Modal>
    </div>
</template>

<style scoped>
@reference '../../../css/app.css';

.input { @apply rounded-xl border border-stone-200 bg-white px-3.5 py-2 text-sm outline-none transition focus:border-pine-400; }
.btn-primary { @apply rounded-full bg-pine-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-pine-800; }
.act { @apply rounded-full px-2.5 py-1 text-xs font-semibold transition; }
</style>
