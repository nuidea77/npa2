<script setup>
import { computed, ref } from 'vue';
import axios from '../bootstrap';
import { t } from '../i18n';
import { useAuthStore } from '../stores/auth';
import DetailHeader from '../components/DetailHeader.vue';
import Icon from '../components/Icon.vue';
import Modal from '../components/Modal.vue';

const auth = useAuthStore();
const aimags = computed(() => auth.settings?.aimags ?? []);
const durations = computed(() => auth.settings?.volunteer_durations ?? []);

const duties = [
    { icon: 'map', title: 'Зам арчилгаа', items: ['Замд унасан мод, хог хаягдлыг цэвэрлэхэд туслах', 'Хүний гарц, шат болон бусад дэд бүтцийг барих, арчлахад туслах', 'Элэгдэл эвдрэл засах, аялагчдад зориулан замыг аюулгүй болгох'] },
    { icon: 'binoculars', title: 'Зэрлэг амьтдыг ажиглах', items: ['Амьтны популяцын талаарх өгөгдөл цуглуулахад туслах зорилгоор зэрлэг амьтдыг ажиглах, хянах', 'Шувуу тоолох болон устаж үгүй болж буй амьтдыг хянахад туслах'] },
    { icon: 'book', title: 'Байгаль орчны боловсрол', items: ['Аялагчдад зориулан аялал, алхалт эсвэл сургалт явуулах', 'ТХГ-ын түүх, экологи болон хамгаалах үйл ажиллагааны талаар мэдээлэл өгөх', 'Байгаль орчныг хамгаалах талаар сурагчдад сургалт орох'] },
    { icon: 'sprout', title: 'Амьдрах орчныг сэргээх', items: ['Зэрлэг ургамал түүх, устгах', 'Экосистемийг сэргээхийн тулд төрөлх ургамлыг дахин тарих', 'Гол цэвэрлэх болон намаг сэргээх төсөлд оролцох'] },
    { icon: 'chat', title: 'Гийчдийн үйлчилгээ', items: ['Аялагч нарт зориулсан мэдээллийн төвд ажиллах', 'Отоглох цэгтэй холбоотой асуултад хариулах, зааварчилгаа өгөх'] },
    { icon: 'building', title: 'Соёлын өвийг хадгалах', items: ['Түүхэн байгууламж, эд өлгийн зүйлсийг хадгалахад туслах', 'Соёлын өвийг баримтжуулах, хадгалахад туслах'] },
    { icon: 'warning', title: 'Хайгуул ба аврах үйл ажиллагааны тусламж', items: ['Байгаль хамгаалагчийн хайгуул, аврах ажиллагааны үед дэмжлэг үзүүлэх (тусгай сургалт шаардлагатай)', 'Осол гарах үед анхны тусламж эсвэл яаралтай хариу үзүүлэхэд оролцох'] },
    { icon: 'eye', title: 'Шинжлэх ухааны судалгааны туслалцаа', items: ['Биологичид, экологичид, геологичдод туслах', 'Дээж, өгөгдөл цуглуулах, туршилтуудыг хийхэд оролцох'] },
    { icon: 'tent', title: 'Отоглох цэгийн зохицуулагч', items: ['Отоглох цэгийн засвар үйлчилгээ хийх, аялагчдыг угтан авах, отоглох газар олоход нь туслах', 'Отоглох цэгийн дүрмийг мөрдөж буй эсэхийг шалгах, төлбөр цуглуулах, засвар үйлчилгээний ажлуудад туслах'] },
];

// --- Хайлт ---
const filters = ref({ aimag: '', park: '', duration: '' });
const results = ref(null);
const searching = ref(false);

async function search() {
    searching.value = true;
    try {
        const { data } = await axios.get('/api/volunteer/search', { params: filters.value });
        results.value = data.orgs;
    } finally {
        searching.value = false;
    }
}

// --- Хүсэлт ---
const showForm = ref(false);
const selectedOrg = ref(null);
const form = ref({ name: '', phone: '', email: '', intro: '', reason: '' });
const errors = ref({});
const sending = ref(false);
const successMsg = ref('');

function openForm(org) {
    selectedOrg.value = org;
    showForm.value = true;
    errors.value = {};
}

async function submit() {
    sending.value = true;
    errors.value = {};
    try {
        const { data } = await axios.post('/api/volunteer/request', {
            ...form.value,
            org_id: selectedOrg.value?.id ?? null,
            duration: filters.value.duration || null,
        });
        showForm.value = false;
        successMsg.value = data.message;
        form.value = { name: '', phone: '', email: '', intro: '', reason: '' };
    } catch (e) {
        errors.value = e.response?.data?.errors ?? {};
    } finally {
        sending.value = false;
    }
}
</script>

<template>
    <div>
        <DetailHeader :title="t('prog.volunteer')" back="/programs" />

        <div class="mx-auto max-w-6xl px-4 py-12">
            <p class="rounded-2xl border border-sand-200 bg-sand-50 p-5 text-sm text-sand-900">
                Сайн дурын байгаль хамгаалагчийн хийх ажил үүрэг нь тухайн сонгосон Хамгаалалтын захиргааны хэрэгцээ,
                шаардлагаас хамаарч янз бүр байх боломжтойг анхаарна уу.
            </p>

            <!-- Үндсэн үүрэг -->
            <h2 class="mt-10 text-xl font-bold text-pine-900 md:text-2xl">Үндсэн үүрэг</h2>
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="d in duties" :key="d.title" class="rounded-2xl border border-stone-200 bg-white p-5">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-pine-100 text-pine-800">
                        <Icon :name="d.icon" :size="22" />
                    </div>
                    <h3 class="mt-2 font-bold text-stone-800">{{ d.title }}</h3>
                    <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-stone-500">
                        <li v-for="(it, i) in d.items" :key="i">{{ it }}</li>
                    </ul>
                </div>
            </div>

            <!-- ТХГ сонгох -->
            <h2 id="search" class="mt-12 scroll-mt-24 text-xl font-bold text-pine-900 md:text-2xl">
                Сайн дурын ажил хийх ТХГ-аа сонгоно уу
            </h2>
            <form class="mt-5 grid gap-3 rounded-2xl border border-pine-100 bg-pine-50/60 p-5 md:grid-cols-4" @submit.prevent="search">
                <select v-model="filters.aimag" class="input">
                    <option value="">Аймаг ({{ t('common.all') }})</option>
                    <option v-for="a in aimags" :key="a" :value="a">{{ a }}</option>
                </select>
                <input v-model="filters.park" class="input" placeholder="ТХГ-ын нэр" />
                <select v-model="filters.duration" class="input">
                    <option value="">Ажиллах хугацаа ({{ t('common.all') }})</option>
                    <option v-for="d in durations" :key="d" :value="d">{{ d }}</option>
                </select>
                <button type="submit" class="rounded-xl bg-pine-700 px-5 py-2.5 font-semibold text-white transition hover:bg-pine-800" :disabled="searching">
                    {{ searching ? t('common.loading') : t('common.search') }}
                </button>
            </form>

            <!-- Хайлтын үр дүн -->
            <div v-if="results !== null" class="mt-6">
                <p v-if="!results.length" class="rounded-2xl bg-stone-100 p-6 text-stone-500">{{ t('common.empty') }}</p>
                <div v-else class="grid gap-4 md:grid-cols-2">
                    <div v-for="org in results" :key="org.id" class="rounded-2xl border border-stone-100 bg-white p-5 shadow-sm">
                        <h3 class="font-bold text-pine-900">{{ org.name }}</h3>
                        <p class="mt-1 text-sm text-stone-500">{{ org.intro }}</p>
                        <div class="mt-3 space-y-1.5 text-sm text-stone-600">
                            <div class="flex items-start gap-2"><Icon name="map-pin" :size="16" class="mt-0.5 shrink-0 text-pine-600" /> {{ org.address }}</div>
                            <div class="flex items-center gap-2"><Icon name="phone" :size="16" class="shrink-0 text-pine-600" /> {{ org.phone }}</div>
                            <div class="flex items-center gap-2"><Icon name="mail" :size="16" class="shrink-0 text-pine-600" /> {{ org.email }}</div>
                            <div v-if="org.parks?.length" class="text-xs text-stone-400">
                                ТХГ: {{ org.parks.map((p) => p.name).join(', ') }}
                            </div>
                        </div>
                        <button
                            class="mt-4 rounded-xl bg-pine-700 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-pine-800"
                            @click="openForm(org)"
                        >Хүсэлт илгээх</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Хүсэлтийн маягт -->
        <Modal :show="showForm" title="Сайн дурын ажлын хүсэлт" @close="showForm = false">
            <div v-if="selectedOrg" class="mb-3 rounded-xl bg-pine-50 p-3 text-sm font-medium text-pine-800">{{ selectedOrg.name }}</div>
            <div class="grid gap-3">
                <div>
                    <input v-model="form.name" class="input w-full" placeholder="Овог, нэр *" />
                    <p v-if="errors.name" class="err">{{ errors.name[0] }}</p>
                </div>
                <div>
                    <input v-model="form.phone" class="input w-full" placeholder="Холбоо барих утас *" />
                    <p v-if="errors.phone" class="err">{{ errors.phone[0] }}</p>
                </div>
                <div>
                    <input v-model="form.email" type="email" class="input w-full" placeholder="И-мэйл хаяг *" />
                    <p v-if="errors.email" class="err">{{ errors.email[0] }}</p>
                </div>
                <div>
                    <textarea v-model="form.intro" rows="3" class="input w-full" placeholder="Өөрийн товч танилцуулга *"></textarea>
                    <p v-if="errors.intro" class="err">{{ errors.intro[0] }}</p>
                </div>
                <div>
                    <textarea v-model="form.reason" rows="3" class="input w-full" placeholder="Яагаад сайн дурын ажил хиймээр байгаа тухай товч бичнэ үү *"></textarea>
                    <p v-if="errors.reason" class="err">{{ errors.reason[0] }}</p>
                </div>
            </div>
            <template #actions>
                <button class="mr-2 rounded-full px-4 py-2 text-sm text-stone-500 hover:bg-stone-100" @click="showForm = false">{{ t('common.cancel') }}</button>
                <button class="rounded-full bg-pine-700 px-5 py-2 text-sm font-semibold text-white hover:bg-pine-800" :disabled="sending" @click="submit">
                    {{ sending ? t('common.loading') : t('common.send') }}
                </button>
            </template>
        </Modal>

        <!-- Амжилтын popup -->
        <Modal :show="!!successMsg" @close="successMsg = ''">
            <div class="text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-pine-100 text-pine-700"><Icon name="check" :size="30" /></div>
                <p class="mt-3 font-medium text-stone-700">{{ successMsg }}</p>
            </div>
        </Modal>
    </div>
</template>

<style scoped>
@reference '../../css/app.css';

.input {
    @apply rounded-xl border border-stone-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-pine-400;
}
.err {
    @apply mt-1 text-xs text-red-600;
}
</style>
