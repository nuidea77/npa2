<script setup>
import { ref } from 'vue';
import axios from '../../bootstrap';
import { useCrud } from '../../composables/useCrud';
import Modal from '../../components/Modal.vue';

const { items, loading, saving, errors, save, remove } = useCrud('events', 'events');

const programs = { khuraldai: 'Хуралдай', junior_ranger: 'Өсвөрийн байгаль хамгаалагч', regional: 'Бүсийн сургалт' };
const stateLabels = { open: 'Нээлттэй', not_started: 'Эхлээгүй', closed: 'Дууссан', soon: 'Тун удахгүй' };
const stateClasses = {
    open: 'bg-pine-100 text-pine-800',
    not_started: 'bg-amber-100 text-amber-800',
    closed: 'bg-stone-200 text-stone-600',
    soon: 'bg-sand-100 text-sand-800',
};
const qTypes = {
    boolean: 'Тийм/Үгүй', text: 'Текст хариулт', link: 'Линк оруулах', image: 'Зураг/файл оруулах',
    single: 'Нэг сонголт', multi: 'Олон сонголт',
};

const form = ref(null);
const regs = ref(null); // бүртгэлүүд харах

function blank() {
    return { program: 'khuraldai', year: new Date().getFullYear(), title: '', description: '', reg_start: '', reg_end: '', status: 'auto', login_required: true, questions: [] };
}

function edit(e) {
    form.value = {
        id: e.id, program: e.program, year: e.year, title: e.title, description: e.description,
        reg_start: e.reg_start?.slice(0, 10), reg_end: e.reg_end?.slice(0, 10),
        status: e.status, login_required: !!e.login_required,
        questions: JSON.parse(JSON.stringify(e.questions ?? [])),
    };
}

function addQuestion() {
    form.value.questions.push({ id: 'q' + Date.now().toString(36), type: 'text', label: '', required: true, options: [], maxWords: null });
}

async function submit() {
    const payload = { ...form.value };
    payload.questions = payload.questions.map((q) => ({
        ...q,
        options: ['single', 'multi'].includes(q.type)
            ? (Array.isArray(q.options) ? q.options : String(q.options ?? '').split('\n')).map((s) => String(s).trim()).filter(Boolean)
            : undefined,
        maxWords: q.type === 'text' && q.maxWords ? Number(q.maxWords) : undefined,
    }));
    if (await save(payload)) form.value = null;
}

async function openRegs(e) {
    const { data } = await axios.get(`/api/admin/events/${e.id}/registrations`);
    regs.value = data;
}

function optionsText(q) {
    return Array.isArray(q.options) ? q.options.join('\n') : (q.options ?? '');
}

function setOptions(q, text) {
    q.options = text.split('\n');
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-pine-900">Сургалт, арга хэмжээ (асуулга)</h1>
            <button class="btn-primary" @click="form = blank()">+ Арга хэмжээ нэмэх</button>
        </div>
        <p class="mt-1 text-sm text-stone-500">Бүртгэлийн огноо нээх/хаах, асуулгын асуултуудыг чөлөөтэй тохируулна. Хэдэн ч асуулт нэмэх боломжтой.</p>

        <div v-if="loading" class="mt-6 text-stone-400">Ачаалж байна...</div>
        <div v-else class="mt-5 grid gap-3">
            <div v-for="e in items" :key="e.id" class="flex items-center gap-4 rounded-2xl border border-stone-100 bg-white p-4 shadow-sm">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="rounded-full bg-stone-100 px-2 py-0.5 font-semibold text-stone-600">{{ programs[e.program] }}</span>
                        <span class="rounded-full px-2 py-0.5 font-bold" :class="stateClasses[e.reg_state]">{{ stateLabels[e.reg_state] }}</span>
                        <span v-if="e.reg_start" class="text-stone-400">{{ e.reg_start?.slice(0, 10) }} — {{ e.reg_end?.slice(0, 10) }}</span>
                        <span v-if="e.login_required" class="text-stone-400">🔒 Нэвтрэлт шаардана</span>
                    </div>
                    <div class="mt-1 font-semibold text-stone-800">{{ e.title }}</div>
                    <div class="text-xs text-stone-400">Асуулт: {{ (e.questions ?? []).length }} · Бүртгэл: {{ e.registrations_count }}</div>
                </div>
                <div class="flex shrink-0 gap-1.5">
                    <button class="act bg-pine-100 text-pine-800 hover:bg-pine-200" @click="openRegs(e)">Бүртгэлүүд ({{ e.registrations_count }})</button>
                    <button class="act bg-stone-100 text-stone-600 hover:bg-stone-200" @click="edit(e)">Засах</button>
                    <button class="act bg-red-50 text-red-600 hover:bg-red-100" @click="remove(e.id)">Устгах</button>
                </div>
            </div>
        </div>

        <!-- Арга хэмжээ нэмэх/засах -->
        <Modal :show="!!form" :title="form?.id ? 'Арга хэмжээ засах' : 'Арга хэмжээ нэмэх'" @close="form = null">
            <div v-if="form" class="grid max-h-[65vh] gap-3 overflow-y-auto pr-1">
                <div class="grid grid-cols-2 gap-3">
                    <select v-model="form.program" class="input">
                        <option value="khuraldai">Хуралдай</option>
                        <option value="junior_ranger">Өсвөрийн байгаль хамгаалагч</option>
                        <option value="regional">Бүсийн сургалт</option>
                    </select>
                    <input v-model.number="form.year" type="number" class="input" placeholder="Жил" />
                </div>
                <div>
                    <input v-model="form.title" class="input w-full" placeholder="Гарчиг *" />
                    <p v-if="errors.title" class="err">{{ errors.title[0] }}</p>
                </div>
                <textarea v-model="form.description" rows="2" class="input" placeholder="Тайлбар"></textarea>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="text-xs text-stone-400">Бүртгэл нээгдэх огноо</label><input v-model="form.reg_start" type="date" class="input w-full" /></div>
                    <div><label class="text-xs text-stone-400">Бүртгэл хаагдах огноо</label><input v-model="form.reg_end" type="date" class="input w-full" /></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <select v-model="form.status" class="input">
                        <option value="auto">Огноогоор автоматаар</option>
                        <option value="open">Нээлттэй (гараар)</option>
                        <option value="closed">Хаагдсан (гараар)</option>
                        <option value="soon">Тун удахгүй</option>
                    </select>
                    <label class="flex items-center gap-2 text-sm text-stone-600">
                        <input v-model="form.login_required" type="checkbox" class="accent-pine-700" /> Нэвтрэхийг шаардах
                    </label>
                </div>

                <!-- Асуултууд -->
                <div class="rounded-xl border border-stone-200 p-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-stone-700">Асуулгын асуултууд</span>
                        <button type="button" class="text-xs font-semibold text-pine-600 hover:underline" @click="addQuestion">+ Асуулт нэмэх</button>
                    </div>
                    <div v-for="(q, i) in form.questions" :key="q.id" class="mt-3 rounded-xl bg-stone-50 p-3">
                        <div class="flex items-center gap-2">
                            <select v-model="q.type" class="input flex-1">
                                <option v-for="(label, val) in qTypes" :key="val" :value="val">{{ label }}</option>
                            </select>
                            <label class="flex shrink-0 items-center gap-1.5 text-xs text-stone-600">
                                <input v-model="q.required" type="checkbox" class="accent-pine-700" /> Заавал
                            </label>
                            <button type="button" class="shrink-0 text-red-500 hover:text-red-700" @click="form.questions.splice(i, 1)">🗑</button>
                        </div>
                        <input v-model="q.label" class="input mt-2 w-full" placeholder="Асуултын текст" />
                        <input
                            v-if="q.type === 'text'"
                            v-model.number="q.maxWords"
                            type="number"
                            class="input mt-2 w-40"
                            placeholder="Үгийн дээд тоо"
                        />
                        <textarea
                            v-if="['single', 'multi'].includes(q.type)"
                            :value="optionsText(q)"
                            rows="3"
                            class="input mt-2 w-full"
                            placeholder="Сонголтууд (мөр бүрт нэг)"
                            @input="setOptions(q, $event.target.value)"
                        ></textarea>
                    </div>
                </div>
            </div>
            <template #actions>
                <button class="mr-2 rounded-full px-4 py-2 text-sm text-stone-500 hover:bg-stone-100" @click="form = null">Болих</button>
                <button class="btn-primary" :disabled="saving" @click="submit">Хадгалах</button>
            </template>
        </Modal>

        <!-- Бүртгэлүүд -->
        <Modal :show="!!regs" :title="regs?.event?.title" @close="regs = null">
            <div v-if="regs" class="max-h-[60vh] overflow-y-auto pr-1">
                <p v-if="!regs.registrations.length" class="text-sm text-stone-400">Бүртгэл алга.</p>
                <div v-for="r in regs.registrations" :key="r.id" class="mb-3 rounded-xl bg-stone-50 p-3 text-sm">
                    <div class="flex justify-between gap-2">
                        <span class="font-semibold text-stone-700">{{ r.user?.name ?? 'Зочин' }} <span class="text-xs font-normal text-stone-400">{{ r.user?.email }}</span></span>
                        <span class="shrink-0 text-xs text-stone-400">{{ r.created_at?.slice(0, 16).replace('T', ' ') }}</span>
                    </div>
                    <dl class="mt-2 space-y-1 text-xs">
                        <div v-for="q in (regs.event.questions ?? [])" :key="q.id">
                            <dt class="text-stone-400">{{ q.label }}</dt>
                            <dd class="font-medium text-stone-700">
                                <template v-if="q.type === 'image'">
                                    <a v-if="r.files?.[q.id]" :href="r.files[q.id]" target="_blank" class="text-pine-600 underline">Файл харах</a>
                                    <span v-else>—</span>
                                </template>
                                <template v-else>
                                    {{ Array.isArray(r.data?.[q.id]) ? r.data[q.id].join(', ') : (r.data?.[q.id] ?? '—') }}
                                </template>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
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
