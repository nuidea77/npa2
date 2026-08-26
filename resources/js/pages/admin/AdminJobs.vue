<script setup>
import { onMounted, ref } from 'vue';
import axios from '../../bootstrap';
import { useCrud } from '../../composables/useCrud';
import Modal from '../../components/Modal.vue';

const { items, loading, saving, errors, save, remove } = useCrud('jobs', 'jobs');
const orgs = ref([]);
const form = ref(null);

onMounted(async () => {
    const { data } = await axios.get('/api/orgs');
    orgs.value = data.orgs;
});

function blank() {
    return { org_id: '', park_name: '', position: '', contract_type: 'Үндсэн', status: 'open', open_date: '', close_date: '', description: '', requirements: '', materials: '', phone: '', email: '' };
}

function edit(j) {
    form.value = { ...j, open_date: j.open_date?.slice(0, 10), close_date: j.close_date?.slice(0, 10) };
}

async function submit() {
    if (await save(form.value)) form.value = null;
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-pine-900">Нээлттэй ажлын байр удирдах</h1>
            <button class="btn-primary" @click="form = blank()">+ Зар нэмэх</button>
        </div>

        <div v-if="loading" class="mt-6 text-stone-400">Ачаалж байна...</div>
        <div v-else class="mt-5 grid gap-3">
            <div v-for="j in items" :key="j.id" class="flex items-center gap-4 rounded-2xl border border-stone-100 bg-white p-4 shadow-sm">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="rounded-full px-2 py-0.5 font-bold" :class="j.status === 'open' ? 'bg-pine-100 text-pine-800' : 'bg-red-100 text-red-700'">
                            {{ j.status === 'open' ? 'Нээлттэй' : 'Хаагдсан' }}
                        </span>
                        <span class="rounded-full bg-stone-100 px-2 py-0.5 text-stone-600">{{ j.contract_type }}</span>
                        <span class="text-stone-400">{{ j.open_date?.slice(0, 10) }} — {{ j.close_date?.slice(0, 10) }}</span>
                    </div>
                    <div class="mt-1 font-semibold text-stone-800">{{ j.position }}</div>
                    <div class="truncate text-xs text-stone-400">{{ j.park_name }} — {{ j.org?.name }}</div>
                </div>
                <div class="flex shrink-0 gap-1.5">
                    <button class="act bg-stone-100 text-stone-600 hover:bg-stone-200" @click="edit(j)">Засах</button>
                    <button class="act bg-red-50 text-red-600 hover:bg-red-100" @click="remove(j.id)">Устгах</button>
                </div>
            </div>
        </div>

        <Modal :show="!!form" :title="form?.id ? 'Зар засах' : 'Зар нэмэх'" @close="form = null">
            <div v-if="form" class="grid max-h-[60vh] gap-3 overflow-y-auto pr-1">
                <select v-model="form.org_id" class="input">
                    <option value="">ХЗ сонгох</option>
                    <option v-for="o in orgs" :key="o.id" :value="o.id">{{ o.name }}</option>
                </select>
                <input v-model="form.park_name" class="input" placeholder="ТХГ-ын нэр" />
                <div>
                    <input v-model="form.position" class="input w-full" placeholder="Албан тушаал *" />
                    <p v-if="errors.position" class="err">{{ errors.position[0] }}</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <select v-model="form.contract_type" class="input">
                        <option>Гэрээт</option>
                        <option>Үндсэн</option>
                        <option>Дадлагажигч оюутан</option>
                    </select>
                    <select v-model="form.status" class="input">
                        <option value="open">Нээлттэй</option>
                        <option value="closed">Хаагдсан</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="text-xs text-stone-400">Нээгдсэн огноо</label><input v-model="form.open_date" type="date" class="input w-full" /></div>
                    <div><label class="text-xs text-stone-400">Хаагдах огноо</label><input v-model="form.close_date" type="date" class="input w-full" /></div>
                </div>
                <textarea v-model="form.description" rows="3" class="input" placeholder="Ажлын байрны тодорхойлолт"></textarea>
                <textarea v-model="form.requirements" rows="2" class="input" placeholder="Тавигдах шаардлага"></textarea>
                <textarea v-model="form.materials" rows="2" class="input" placeholder="Бүрдүүлэх материал"></textarea>
                <div class="grid grid-cols-2 gap-3">
                    <input v-model="form.phone" class="input" placeholder="Утас" />
                    <input v-model="form.email" class="input" placeholder="И-мэйл" />
                </div>
            </div>
            <template #actions>
                <button class="mr-2 rounded-full px-4 py-2 text-sm text-stone-500 hover:bg-stone-100" @click="form = null">Болих</button>
                <button class="btn-primary" :disabled="saving" @click="submit">Хадгалах</button>
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
