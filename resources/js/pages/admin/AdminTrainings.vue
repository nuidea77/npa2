<script setup>
import { computed, ref } from 'vue';
import { useCrud } from '../../composables/useCrud';
import { useAuthStore } from '../../stores/auth';
import Modal from '../../components/Modal.vue';
import Icon from '../../components/Icon.vue';

const auth = useAuthStore();
const { items, loading, saving, errors, save, remove } = useCrud('trainings', 'trainings');

const positions = computed(() => auth.settings?.positions ?? []);
const regions = ['Баруун бүс', 'Хангайн бүс', 'Төвийн бүс', 'Зүүн бүс', 'Говийн бүс'];
const types = { youtube: { icon: 'play', label: 'YouTube' }, pdf: { icon: 'file-text', label: 'PDF' }, image: { icon: 'image', label: 'Зураг' } };

const form = ref(null);
const file = ref(null);
const cover = ref(null);

function blank() {
    return { title: '', type: 'youtube', url: '', summary: '', published_at: new Date().toISOString().slice(0, 10), positions: [], regions: [], cover_url: '' };
}

function edit(t) {
    form.value = { ...t, published_at: t.published_at?.slice(0, 10), positions: t.positions ?? [], regions: t.regions ?? [], cover_url: t.cover };
    file.value = null;
    cover.value = null;
}

async function submit() {
    const payload = { ...form.value };
    if (file.value) payload.file = file.value;
    if (cover.value) payload.cover = cover.value;
    if (await save(payload, { asForm: true })) form.value = null;
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-pine-900">Сургалтын материал</h1>
            <button class="btn-primary" @click="form = blank(); file = null; cover = null">+ Сургалт нэмэх</button>
        </div>
        <p class="mt-1 text-sm text-stone-500">YouTube линк, PDF, зураг форматаар. Албан тушаал болон ХЗ-ны бүсээр хэнд харагдахыг хязгаарлаж болно (хоосон = бүгдэд).</p>

        <div v-if="loading" class="mt-6 text-stone-400">Ачаалж байна...</div>
        <div v-else class="mt-5 grid gap-3">
            <div v-for="tr in items" :key="tr.id" class="flex items-center gap-4 rounded-2xl border border-stone-100 bg-white p-4 shadow-sm">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="flex items-center gap-1 rounded-full bg-stone-100 px-2 py-0.5 font-semibold text-stone-600"><Icon :name="types[tr.type]?.icon ?? 'file-text'" :size="12" /> {{ types[tr.type]?.label ?? tr.type }}</span>
                        <span class="text-stone-400">{{ tr.published_at?.slice(0, 10) }}</span>
                        <span v-if="tr.positions?.length" class="text-stone-400">· {{ tr.positions.join(', ') }}</span>
                        <span v-if="tr.regions?.length" class="text-stone-400">· {{ tr.regions.join(', ') }}</span>
                    </div>
                    <div class="mt-1 font-semibold text-stone-800">{{ tr.title }}</div>
                    <div class="truncate text-xs text-stone-400">{{ tr.url }}</div>
                </div>
                <div class="flex shrink-0 gap-1.5">
                    <button class="act bg-stone-100 text-stone-600 hover:bg-stone-200" @click="edit(tr)">Засах</button>
                    <button class="act bg-red-50 text-red-600 hover:bg-red-100" @click="remove(tr.id)">Устгах</button>
                </div>
            </div>
        </div>

        <Modal :show="!!form" :title="form?.id ? 'Сургалт засах' : 'Сургалт нэмэх'" @close="form = null">
            <div v-if="form" class="grid max-h-[60vh] gap-3 overflow-y-auto pr-1">
                <div>
                    <input v-model="form.title" class="input w-full" placeholder="Гарчиг *" />
                    <p v-if="errors.title" class="err">{{ errors.title[0] }}</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <select v-model="form.type" class="input">
                        <option value="youtube">YouTube видео</option>
                        <option value="pdf">PDF</option>
                        <option value="image">Зураг</option>
                    </select>
                    <input v-model="form.published_at" type="date" class="input" />
                </div>
                <input v-if="form.type === 'youtube'" v-model="form.url" class="input" placeholder="YouTube линк (https://www.youtube.com/watch?v=...)" />
                <div v-else>
                    <label class="text-xs text-stone-400">Файл байршуулах (PDF/JPEG/PNG, 20MB хүртэл)</label>
                    <input type="file" accept=".pdf,image/*" class="input w-full" @change="file = $event.target.files?.[0] ?? null" />
                    <input v-model="form.url" class="input mt-2 w-full" placeholder="эсвэл файлын зам/линк" />
                </div>
                <textarea v-model="form.summary" rows="2" class="input" placeholder="Товч агуулга"></textarea>
                <div>
                    <label class="text-xs text-stone-400">Cover зураг</label>
                    <input type="file" accept="image/*" class="input w-full" @change="cover = $event.target.files?.[0] ?? null" />
                </div>
                <div>
                    <label class="text-xs text-stone-400">Харах эрхтэй албан тушаал (хоосон = бүгд)</label>
                    <div class="mt-1 grid gap-1">
                        <label v-for="p in positions" :key="p" class="flex items-center gap-1.5 text-xs text-stone-600">
                            <input v-model="form.positions" type="checkbox" :value="p" class="accent-pine-700" /> {{ p }}
                        </label>
                    </div>
                </div>
                <div>
                    <label class="text-xs text-stone-400">Харах эрхтэй бүс (хоосон = бүгд)</label>
                    <div class="mt-1 grid grid-cols-2 gap-1">
                        <label v-for="r in regions" :key="r" class="flex items-center gap-1.5 text-xs text-stone-600">
                            <input v-model="form.regions" type="checkbox" :value="r" class="accent-pine-700" /> {{ r }}
                        </label>
                    </div>
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
