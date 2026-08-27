<script setup>
import { computed, onMounted, ref } from 'vue';
import axios from '../../bootstrap';
import { useCrud } from '../../composables/useCrud';
import { useAuthStore } from '../../stores/auth';
import Modal from '../../components/Modal.vue';

const auth = useAuthStore();
const { items, loading, saving, errors, save, remove } = useCrud('parks', 'parks');

const orgs = ref([]);
const aimags = computed(() => auth.settings?.aimags ?? []);
const types = computed(() => auth.settings?.park_types ?? []);

const form = ref(null);
const imageFile = ref(null);

const textFields = [
    ['intro', 'Танилцуулга'],
    ['highlights', 'Онцолж буй байгалийн тогтоц газар'],
    ['animals', 'Амьтад'],
    ['geography', 'Газар зүйн онцлог'],
    ['locals', 'Нутгийн зон олон'],
    ['get_there', 'Хэрхэн хүрч очих вэ?'],
    ['travel', 'Хэрхэн аялах вэ?'],
    ['services', 'Аялал жуулчлалын үйлчилгээ'],
    ['warnings', 'Анхааруулга, уриалга'],
    ['admin_info', 'Хамгаалалтын захиргаа'],
];

onMounted(async () => {
    const { data } = await axios.get('/api/orgs');
    orgs.value = data.orgs;
});

function blank() {
    const b = { org_id: '', name: '', aimag: '', type: '', lat: null, lng: null, featured: false, image_url: '' };
    for (const [k] of textFields) b[k] = '';
    return b;
}

function edit(p) {
    form.value = { ...p, image_url: p.image };
    imageFile.value = null;
}

async function submit() {
    const payload = { ...form.value };
    delete payload.org;
    if (imageFile.value) payload.image = imageFile.value;
    if (await save(payload, { asForm: true })) form.value = null;
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-pine-900">Тусгай хамгаалалттай газрууд</h1>
            <button class="btn-primary" @click="form = blank(); imageFile = null">+ ТХГ нэмэх</button>
        </div>

        <div v-if="loading" class="mt-6 text-stone-400">Ачаалж байна...</div>
        <div v-else class="mt-5 grid gap-3">
            <div v-for="p in items" :key="p.id" class="flex items-center gap-4 rounded-2xl border border-stone-100 bg-white p-4 shadow-sm">
                <img :src="p.image || '/images/park-mountain.svg'" class="h-14 w-20 shrink-0 rounded-xl object-cover" alt="" />
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-1.5 font-semibold text-stone-800">{{ p.name }} <Icon v-if="p.featured" name="star" :size="15" class="text-sand-600" title="Нүүрт онцолсон" /></div>
                    <div class="text-xs text-stone-400">{{ p.type }} · {{ p.aimag }} · {{ p.org?.name }}</div>
                </div>
                <div class="flex shrink-0 gap-1.5">
                    <button class="act bg-stone-100 text-stone-600 hover:bg-stone-200" @click="edit(p)">Засах</button>
                    <button class="act bg-red-50 text-red-600 hover:bg-red-100" @click="remove(p.id)">Устгах</button>
                </div>
            </div>
        </div>

        <Modal :show="!!form" :title="form?.id ? 'ТХГ засах' : 'ТХГ нэмэх'" @close="form = null">
            <div v-if="form" class="grid max-h-[60vh] gap-3 overflow-y-auto pr-1">
                <div>
                    <input v-model="form.name" class="input w-full" placeholder="ТХГ-ын нэр *" />
                    <p v-if="errors.name" class="err">{{ errors.name[0] }}</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <select v-model="form.aimag" class="input">
                        <option value="">Аймаг</option>
                        <option v-for="a in aimags" :key="a">{{ a }}</option>
                    </select>
                    <select v-model="form.type" class="input">
                        <option value="">Төрөл</option>
                        <option v-for="ty in types" :key="ty">{{ ty }}</option>
                    </select>
                </div>
                <select v-model="form.org_id" class="input">
                    <option value="">Хамгаалалтын захиргаа</option>
                    <option v-for="o in orgs" :key="o.id" :value="o.id">{{ o.name }}</option>
                </select>
                <div class="grid grid-cols-2 gap-3">
                    <input v-model.number="form.lat" type="number" step="0.000001" class="input" placeholder="Өргөрөг (lat)" />
                    <input v-model.number="form.lng" type="number" step="0.000001" class="input" placeholder="Уртраг (lng)" />
                </div>
                <div>
                    <label class="text-xs text-stone-400">Зураг (файл эсвэл зам)</label>
                    <input type="file" accept="image/*" class="input w-full" @change="imageFile = $event.target.files?.[0] ?? null" />
                    <input v-model="form.image_url" class="input mt-2 w-full" placeholder="эсвэл: /images/park-lake.svg" />
                </div>
                <label class="flex items-center gap-2 text-sm text-stone-600">
                    <input v-model="form.featured" type="checkbox" class="accent-pine-700" /> Нүүр хуудсанд онцлох
                </label>
                <div v-for="[k, label] in textFields" :key="k">
                    <label class="text-xs text-stone-400">{{ label }}</label>
                    <textarea v-model="form[k]" :rows="k === 'intro' ? 3 : 2" class="input w-full"></textarea>
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
