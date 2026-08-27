<script setup>
import { onMounted, ref } from 'vue';
import axios from '../../bootstrap';
import { useAuthStore } from '../../stores/auth';
import Icon from '../../components/Icon.vue';

const auth = useAuthStore();

const menu = ref({});
const feedbackTypes = ref([]);
const feedbackSubtypes = ref([]);
const admins = ref([]);
const saved = ref(false);
const newType = ref('');
const newSubtype = ref('');

const menuItems = [
    ['about', 'Бидний тухай'],
    ['programs', 'Хөтөлбөрүүд'],
    ['parks', 'Тусгай Хамгаалалттай Газрууд'],
    ['news', 'Мэдээ'],
    ['help', 'Тусламж'],
];

onMounted(async () => {
    const { data } = await axios.get('/api/admin/settings');
    menu.value = data.menu ?? {};
    feedbackTypes.value = data.feedback_types ?? [];
    feedbackSubtypes.value = data.feedback_subtypes ?? [];

    if (auth.user?.role === 'superadmin') {
        const a = await axios.get('/api/admin/admins');
        admins.value = a.data.admins;
    }
});

async function save() {
    await axios.post('/api/admin/settings', {
        menu: menu.value,
        feedback_types: feedbackTypes.value,
        feedback_subtypes: feedbackSubtypes.value,
    });
    await auth.fetchSettings();
    saved.value = true;
    setTimeout(() => (saved.value = false), 2000);
}

function addItem(list, val) {
    const v = val.value.trim();
    if (v && !list.value.includes(v)) list.value.push(v);
    val.value = '';
}
</script>

<template>
    <div>
        <h1 class="text-xl font-bold text-pine-900">Тохиргоо</h1>

        <!-- Цэсний харагдац -->
        <section class="mt-5 rounded-2xl border border-stone-100 bg-white p-5 shadow-sm">
            <h2 class="font-bold text-stone-800">Вэбийн цэс удирдах</h2>
            <p class="mt-1 text-xs text-stone-400">Цэсийг нуухад тухайн хэсэг вэб дээр харагдахгүй болно.</p>
            <div class="mt-3 grid gap-2">
                <label v-for="[key, label] in menuItems" :key="key" class="flex items-center justify-between rounded-xl bg-stone-50 px-4 py-2.5 text-sm">
                    <span class="font-medium text-stone-700">{{ label }}</span>
                    <input
                        type="checkbox"
                        class="h-4 w-4 accent-pine-700"
                        :checked="menu[key] !== false"
                        @change="menu[key] = $event.target.checked"
                    />
                </label>
            </div>
        </section>

        <!-- Санал хүсэлтийн төрлүүд -->
        <section class="mt-5 grid gap-5 md:grid-cols-2">
            <div class="rounded-2xl border border-stone-100 bg-white p-5 shadow-sm">
                <h2 class="font-bold text-stone-800">Санал хүсэлтийн үндсэн төрөл</h2>
                <div class="mt-3 flex flex-wrap gap-2">
                    <span v-for="(ty, i) in feedbackTypes" :key="ty" class="flex items-center gap-1.5 rounded-full bg-pine-100 px-3 py-1 text-xs font-semibold text-pine-800">
                        {{ ty }}
                        <button class="text-pine-500 hover:text-red-600" @click="feedbackTypes.splice(i, 1)"><Icon name="x" :size="12" /></button>
                    </span>
                </div>
                <div class="mt-3 flex gap-2">
                    <input v-model="newType" class="input flex-1" placeholder="Шинэ төрөл..." @keyup.enter="addItem(feedbackTypes, $refs)" />
                    <button class="btn-secondary" @click="feedbackTypes.includes(newType.trim()) || !newType.trim() ? null : (feedbackTypes.push(newType.trim()), newType = '')">Нэмэх</button>
                </div>
            </div>

            <div class="rounded-2xl border border-stone-100 bg-white p-5 shadow-sm">
                <h2 class="font-bold text-stone-800">Санал хүсэлтийн дэд сэдэв</h2>
                <div class="mt-3 flex flex-wrap gap-2">
                    <span v-for="(st, i) in feedbackSubtypes" :key="st" class="flex items-center gap-1.5 rounded-full bg-sand-100 px-3 py-1 text-xs font-semibold text-sand-800">
                        {{ st }}
                        <button class="text-sand-600 hover:text-red-600" @click="feedbackSubtypes.splice(i, 1)"><Icon name="x" :size="12" /></button>
                    </span>
                </div>
                <div class="mt-3 flex gap-2">
                    <input v-model="newSubtype" class="input flex-1" placeholder="Шинэ дэд сэдэв..." />
                    <button class="btn-secondary" @click="feedbackSubtypes.includes(newSubtype.trim()) || !newSubtype.trim() ? null : (feedbackSubtypes.push(newSubtype.trim()), newSubtype = '')">Нэмэх</button>
                </div>
            </div>
        </section>

        <div class="mt-5 flex items-center gap-3">
            <button class="btn-primary" @click="save">Тохиргоо хадгалах</button>
            <span v-if="saved" class="flex items-center gap-1.5 text-sm font-medium text-pine-700"><Icon name="check" :size="16" /> Хадгалагдлаа</span>
        </div>

        <!-- Админ эрхтэй хэрэглэгчид -->
        <section v-if="auth.user?.role === 'superadmin'" class="mt-8 rounded-2xl border border-stone-100 bg-white p-5 shadow-sm">
            <h2 class="font-bold text-stone-800">Админ эрхтэй хэрэглэгчид</h2>
            <p class="mt-1 text-xs text-stone-400">Админ нэмэх, хасах, эрхийн тохируулгыг «Хэрэглэгчид» хэсгийн Эрх баганаас хийнэ.</p>
            <div class="mt-3 grid gap-2">
                <div v-for="a in admins" :key="a.id" class="flex items-center justify-between rounded-xl bg-stone-50 px-4 py-2.5 text-sm">
                    <div>
                        <span class="font-medium text-stone-700">{{ a.name }}</span>
                        <span class="ml-2 text-xs text-stone-400">{{ a.email }}</span>
                    </div>
                    <span class="rounded-full px-2.5 py-0.5 text-xs font-bold" :class="a.role === 'superadmin' ? 'bg-pine-700 text-white' : 'bg-pine-100 text-pine-800'">
                        {{ a.role === 'superadmin' ? 'Супер админ' : 'Админ' }}
                    </span>
                </div>
            </div>
            <router-link to="/admin/users" class="mt-3 inline-block text-sm font-semibold text-pine-600 hover:underline">→ Хэрэглэгчид рүү очих</router-link>
        </section>
    </div>
</template>

<style scoped>
@reference '../../../css/app.css';

.input { @apply rounded-xl border border-stone-200 bg-white px-3.5 py-2 text-sm outline-none transition focus:border-pine-400; }
.btn-primary { @apply rounded-full bg-pine-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-pine-800; }
.btn-secondary { @apply rounded-full bg-stone-100 px-4 py-2 text-sm font-semibold text-stone-600 transition hover:bg-stone-200; }
</style>
