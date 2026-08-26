<script setup>
import { computed, ref } from 'vue';
import { useCrud } from '../../composables/useCrud';
import { useAuthStore } from '../../stores/auth';
import Modal from '../../components/Modal.vue';

const auth = useAuthStore();
const { items, loading, saving, errors, save, remove } = useCrud('orgs', 'orgs');

const aimags = computed(() => auth.settings?.aimags ?? []);
const durations = computed(() => auth.settings?.volunteer_durations ?? []);
const regions = ['Баруун бүс', 'Хангайн бүс', 'Төвийн бүс', 'Зүүн бүс', 'Говийн бүс'];

const form = ref(null);

function blank() {
    return { name: '', region: '', aimags: [], phone: '', email: '', address: '', intro: '', accepts_volunteers: true, volunteer_durations: [...durations.value] };
}

function edit(o) {
    form.value = { ...o, aimags: o.aimags ?? [], volunteer_durations: o.volunteer_durations ?? [] };
}

async function submit() {
    if (await save(form.value)) form.value = null;
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-pine-900">Хамгаалалтын захиргаад (ХЗ)</h1>
            <button class="btn-primary" @click="form = blank()">+ ХЗ нэмэх</button>
        </div>
        <p class="mt-1 text-sm text-stone-500">Шинээр нэмэгдэх ХЗ-дыг нэмэх, нэрийг засварлах, устгах.</p>

        <div v-if="loading" class="mt-6 text-stone-400">Ачаалж байна...</div>
        <div v-else class="mt-5 grid gap-3">
            <div v-for="o in items" :key="o.id" class="flex items-center gap-4 rounded-2xl border border-stone-100 bg-white p-4 shadow-sm">
                <div class="min-w-0 flex-1">
                    <div class="font-semibold text-stone-800">{{ o.name }}</div>
                    <div class="mt-0.5 flex flex-wrap gap-2 text-xs text-stone-400">
                        <span>{{ o.region }}</span>
                        <span>· {{ (o.aimags ?? []).join(', ') }}</span>
                        <span>· ТХГ: {{ o.parks_count }}</span>
                        <span v-if="!o.accepts_volunteers" class="text-red-500">· Сайн дурын ажилтан авахгүй</span>
                    </div>
                    <div class="mt-0.5 text-xs text-stone-400">{{ o.phone }} · {{ o.email }}</div>
                </div>
                <div class="flex shrink-0 gap-1.5">
                    <button class="act bg-stone-100 text-stone-600 hover:bg-stone-200" @click="edit(o)">Засах</button>
                    <button class="act bg-red-50 text-red-600 hover:bg-red-100" @click="remove(o.id)">Устгах</button>
                </div>
            </div>
        </div>

        <Modal :show="!!form" :title="form?.id ? 'ХЗ засах' : 'ХЗ нэмэх'" @close="form = null">
            <div v-if="form" class="grid max-h-[60vh] gap-3 overflow-y-auto pr-1">
                <div>
                    <input v-model="form.name" class="input w-full" placeholder="ХЗ-ны нэр *" />
                    <p v-if="errors.name" class="err">{{ errors.name[0] }}</p>
                </div>
                <select v-model="form.region" class="input">
                    <option value="">Бүс сонгох</option>
                    <option v-for="r in regions" :key="r">{{ r }}</option>
                </select>
                <div>
                    <label class="text-xs text-stone-400">Харьяалагдах аймгууд</label>
                    <div class="mt-1 grid max-h-36 grid-cols-2 gap-1 overflow-y-auto rounded-xl border border-stone-200 p-2">
                        <label v-for="a in aimags" :key="a" class="flex items-center gap-1.5 text-xs text-stone-600">
                            <input v-model="form.aimags" type="checkbox" :value="a" class="accent-pine-700" /> {{ a }}
                        </label>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <input v-model="form.phone" class="input" placeholder="Утас" />
                    <input v-model="form.email" class="input" placeholder="И-мэйл" />
                </div>
                <input v-model="form.address" class="input" placeholder="Хаяг байршил" />
                <textarea v-model="form.intro" rows="2" class="input" placeholder="Танилцуулга"></textarea>
                <label class="flex items-center gap-2 text-sm text-stone-600">
                    <input v-model="form.accepts_volunteers" type="checkbox" class="accent-pine-700" /> Сайн дурын ажилтан авна
                </label>
                <div v-if="form.accepts_volunteers">
                    <label class="text-xs text-stone-400">Сайн дурын ажлын боломжит хугацаа</label>
                    <div class="mt-1 grid gap-1">
                        <label v-for="d in durations" :key="d" class="flex items-center gap-1.5 text-xs text-stone-600">
                            <input v-model="form.volunteer_durations" type="checkbox" :value="d" class="accent-pine-700" /> {{ d }}
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
