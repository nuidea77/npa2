<script setup>
import { ref } from 'vue';
import { useCrud } from '../../composables/useCrud';
import Modal from '../../components/Modal.vue';

const { items, loading, saving, errors, save, remove } = useCrud('news', 'news');

const form = ref(null);
const imageFile = ref(null);

function blank() {
    return { title: '', title_en: '', body: '', type: 'internal', external_url: '', published_at: new Date().toISOString().slice(0, 10), status: 'active', image_url: '' };
}

function edit(n) {
    form.value = { id: n.id, title: n.title, title_en: n.title_en, body: n.body, type: n.type, external_url: n.external_url, published_at: n.published_at?.slice(0, 10), status: n.status, image_url: n.image };
    imageFile.value = null;
}

async function submit() {
    const payload = { ...form.value };
    if (imageFile.value) payload.image = imageFile.value;
    if (await save(payload, { asForm: true })) form.value = null;
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-pine-900">Мэдээ удирдах</h1>
            <button class="btn-primary" @click="form = blank(); imageFile = null">+ Мэдээ нэмэх</button>
        </div>
        <p class="mt-1 text-sm text-stone-500">Мэдээ 2 төрөлтэй: дотоод (дэлгэрэнгүй хуудастай) болон гадаад линк (ж: gogo.mn руу үсэрдэг).</p>

        <div v-if="loading" class="mt-6 text-stone-400">Ачаалж байна...</div>
        <div v-else class="mt-5 grid gap-3">
            <div v-for="n in items" :key="n.id" class="flex items-center gap-4 rounded-2xl border border-stone-100 bg-white p-4 shadow-sm">
                <img :src="n.image || '/images/news-external.svg'" class="h-16 w-24 shrink-0 rounded-xl object-cover" alt="" />
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="text-stone-400">{{ n.published_at?.slice(0, 10) }}</span>
                        <span class="rounded-full px-2 py-0.5 font-semibold" :class="n.type === 'external' ? 'bg-sand-100 text-sand-800' : 'bg-pine-100 text-pine-800'">
                            {{ n.type === 'external' ? 'Гадаад линк ↗' : 'Дотоод' }}
                        </span>
                        <span v-if="n.status === 'hidden'" class="rounded-full bg-stone-200 px-2 py-0.5 font-semibold text-stone-600">Нуусан</span>
                    </div>
                    <div class="mt-1 truncate font-semibold text-stone-800">{{ n.title }}</div>
                </div>
                <div class="flex shrink-0 gap-1.5">
                    <button class="act bg-stone-100 text-stone-600 hover:bg-stone-200" @click="edit(n)">Засах</button>
                    <button class="act bg-red-50 text-red-600 hover:bg-red-100" @click="remove(n.id)">Устгах</button>
                </div>
            </div>
        </div>

        <Modal :show="!!form" :title="form?.id ? 'Мэдээ засах' : 'Мэдээ нэмэх'" @close="form = null">
            <div v-if="form" class="grid max-h-[60vh] gap-3 overflow-y-auto pr-1">
                <div>
                    <input v-model="form.title" class="input w-full" placeholder="Гарчиг *" />
                    <p v-if="errors.title" class="err">{{ errors.title[0] }}</p>
                </div>
                <input v-model="form.title_en" class="input w-full" placeholder="Гарчиг (EN)" />
                <div class="grid grid-cols-2 gap-3">
                    <select v-model="form.type" class="input">
                        <option value="internal">Дотоод мэдээ</option>
                        <option value="external">Гадаад линк</option>
                    </select>
                    <input v-model="form.published_at" type="date" class="input" />
                </div>
                <div v-if="form.type === 'external'">
                    <input v-model="form.external_url" class="input w-full" placeholder="Гадаад линк (https://gogo.mn/...) *" />
                    <p v-if="errors.external_url" class="err">{{ errors.external_url[0] }}</p>
                </div>
                <textarea v-else v-model="form.body" rows="7" class="input w-full" placeholder="Мэдээний гол текст"></textarea>
                <div>
                    <label class="text-xs text-stone-400">Зураг (файл эсвэл зам)</label>
                    <input type="file" accept="image/*" class="input w-full" @change="imageFile = $event.target.files?.[0] ?? null" />
                    <input v-model="form.image_url" class="input mt-2 w-full" placeholder="эсвэл зургийн зам: /images/....svg" />
                </div>
                <select v-model="form.status" class="input">
                    <option value="active">Идэвхтэй</option>
                    <option value="hidden">Нуух</option>
                </select>
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
