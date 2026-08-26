<script setup>
import { ref } from 'vue';
import { useCrud } from '../../composables/useCrud';
import Modal from '../../components/Modal.vue';

const { items, loading, saving, errors, save, remove } = useCrud('faqs', 'faqs');
const form = ref(null);

function blank() {
    return { question: '', answer: '', question_en: '', answer_en: '', ord: (items.value.length + 1) };
}

async function submit() {
    if (await save(form.value)) form.value = null;
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-pine-900">Түгээмэл асуулт (FAQ)</h1>
            <button class="btn-primary" @click="form = blank()">+ Асуулт нэмэх</button>
        </div>

        <div v-if="loading" class="mt-6 text-stone-400">Ачаалж байна...</div>
        <div v-else class="mt-5 grid gap-3">
            <div v-for="f in items" :key="f.id" class="rounded-2xl border border-stone-100 bg-white p-4 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="font-semibold text-stone-800">{{ f.ord }}. {{ f.question }}</div>
                        <p class="mt-1 line-clamp-2 text-sm text-stone-500">{{ f.answer }}</p>
                    </div>
                    <div class="flex shrink-0 gap-1.5">
                        <button class="act bg-stone-100 text-stone-600 hover:bg-stone-200" @click="form = { ...f }">Засах</button>
                        <button class="act bg-red-50 text-red-600 hover:bg-red-100" @click="remove(f.id)">Устгах</button>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="!!form" :title="form?.id ? 'FAQ засах' : 'FAQ нэмэх'" @close="form = null">
            <div v-if="form" class="grid max-h-[60vh] gap-3 overflow-y-auto pr-1">
                <div>
                    <textarea v-model="form.question" rows="2" class="input w-full" placeholder="Асуулт *"></textarea>
                    <p v-if="errors.question" class="err">{{ errors.question[0] }}</p>
                </div>
                <div>
                    <textarea v-model="form.answer" rows="4" class="input w-full" placeholder="Хариулт *"></textarea>
                    <p v-if="errors.answer" class="err">{{ errors.answer[0] }}</p>
                </div>
                <textarea v-model="form.question_en" rows="2" class="input" placeholder="Асуулт (EN)"></textarea>
                <textarea v-model="form.answer_en" rows="3" class="input" placeholder="Хариулт (EN)"></textarea>
                <input v-model.number="form.ord" type="number" class="input w-32" placeholder="Дараалал" />
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
