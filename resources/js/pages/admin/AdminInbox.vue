<script setup>
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import axios from '../../bootstrap';
import Modal from '../../components/Modal.vue';
import Icon from '../../components/Icon.vue';

const route = useRoute();
const tab = ref(route.query.tab === 'volunteers' ? 'volunteers' : 'feedback');

const feedback = ref([]);
const volunteers = ref([]);
const loading = ref(true);
const fbFilters = ref({ type: '', status: '' });
const detail = ref(null);

async function load() {
    loading.value = true;
    try {
        const [f, v] = await Promise.all([
            axios.get('/api/admin/feedback', { params: fbFilters.value }),
            axios.get('/api/admin/volunteers'),
        ]);
        feedback.value = f.data.feedback;
        volunteers.value = v.data.volunteers;
    } finally {
        loading.value = false;
    }
}

onMounted(load);

async function setFbStatus(fb, status) {
    await axios.put(`/api/admin/feedback/${fb.id}`, { status, reply: fb.reply });
    await load();
}

async function saveReply() {
    await axios.put(`/api/admin/feedback/${detail.value.id}`, { status: 'answered', reply: detail.value.reply });
    detail.value = null;
    await load();
}

async function setVolStatus(v, status) {
    await axios.put(`/api/admin/volunteers/${v.id}`, { status });
    await load();
}
</script>

<template>
    <div>
        <h1 class="text-xl font-bold text-pine-900">Ирсэн хүсэлтүүд</h1>

        <div class="mt-4 flex gap-2">
            <button class="tab flex items-center gap-1.5" :class="{ active: tab === 'feedback' }" @click="tab = 'feedback'"><Icon name="chat" :size="15" /> Санал хүсэлт, талархал</button>
            <button class="tab flex items-center gap-1.5" :class="{ active: tab === 'volunteers' }" @click="tab = 'volunteers'"><Icon name="sprout" :size="15" /> Сайн дурын хүсэлтүүд</button>
        </div>

        <div v-if="loading" class="mt-6 text-stone-400">Ачаалж байна...</div>

        <!-- Санал хүсэлт -->
        <div v-else-if="tab === 'feedback'">
            <div class="mt-4 flex flex-wrap gap-2">
                <select v-model="fbFilters.type" class="input" @change="load">
                    <option value="">Бүх төрөл</option>
                    <option>Санал, хүсэлт</option>
                    <option>Гомдол</option>
                    <option>Талархал</option>
                </select>
                <select v-model="fbFilters.status" class="input" @change="load">
                    <option value="">Бүх статус</option>
                    <option value="unanswered">Хариу өгөөгүй</option>
                    <option value="answered">Хариу өгсөн</option>
                </select>
            </div>

            <div class="mt-4 grid gap-3">
                <p v-if="!feedback.length" class="rounded-2xl bg-stone-100 p-5 text-sm text-stone-500">Санал хүсэлт алга.</p>
                <div v-for="fb in feedback" :key="fb.id" class="rounded-2xl border border-stone-100 bg-white p-4 shadow-sm">
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="rounded-full bg-pine-100 px-2 py-0.5 font-semibold text-pine-800">{{ fb.type }}</span>
                        <span v-if="fb.subtype" class="rounded-full bg-stone-100 px-2 py-0.5 text-stone-600">{{ fb.subtype }}</span>
                        <span
                            class="rounded-full px-2 py-0.5 font-bold"
                            :class="fb.status === 'answered' ? 'bg-pine-100 text-pine-800' : 'bg-amber-100 text-amber-800'"
                        >{{ fb.status === 'answered' ? 'Хариу өгсөн' : 'Хариу өгөөгүй' }}</span>
                        <span class="ml-auto text-stone-400">{{ fb.created_at?.slice(0, 16).replace('T', ' ') }}</span>
                    </div>
                    <p class="mt-2 text-sm text-stone-700">{{ fb.message }}</p>
                    <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-stone-400">
                        <span class="flex items-center gap-1"><Icon name="mail" :size="13" /> {{ fb.email }}</span>
                        <span v-if="fb.phone" class="flex items-center gap-1"><Icon name="phone" :size="13" /> {{ fb.phone }}</span>
                        <span v-if="fb.user" class="flex items-center gap-1"><Icon name="user" :size="13" /> {{ fb.user.name }}</span>
                    </div>
                    <div class="mt-3 flex gap-1.5">
                        <button class="act bg-pine-100 text-pine-800 hover:bg-pine-200" @click="detail = { ...fb }">Хариу бичих</button>
                        <button
                            v-if="fb.status === 'unanswered'"
                            class="act bg-stone-100 text-stone-600 hover:bg-stone-200"
                            @click="setFbStatus(fb, 'answered')"
                        >Хариу өгсөн болгох</button>
                        <button
                            v-else
                            class="act bg-stone-100 text-stone-600 hover:bg-stone-200"
                            @click="setFbStatus(fb, 'unanswered')"
                        >Хариу өгөөгүй болгох</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Сайн дурын хүсэлтүүд -->
        <div v-else class="mt-4 grid gap-3">
            <p v-if="!volunteers.length" class="rounded-2xl bg-stone-100 p-5 text-sm text-stone-500">Хүсэлт алга.</p>
            <div v-for="v in volunteers" :key="v.id" class="rounded-2xl border border-stone-100 bg-white p-4 shadow-sm">
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span
                        class="rounded-full px-2 py-0.5 font-bold"
                        :class="v.status === 'replied' ? 'bg-pine-100 text-pine-800' : 'bg-amber-100 text-amber-800'"
                    >{{ v.status === 'replied' ? 'Холбогдсон' : 'Шинэ' }}</span>
                    <span v-if="v.duration" class="rounded-full bg-stone-100 px-2 py-0.5 text-stone-600">{{ v.duration }}</span>
                    <span class="ml-auto text-stone-400">{{ v.created_at?.slice(0, 16).replace('T', ' ') }}</span>
                </div>
                <div class="mt-2 font-semibold text-stone-800">{{ v.name }}</div>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-stone-400">
                    <span class="flex items-center gap-1"><Icon name="phone" :size="13" /> {{ v.phone }}</span>
                    <span class="flex items-center gap-1"><Icon name="mail" :size="13" /> {{ v.email }}</span>
                    <span v-if="v.org" class="flex items-center gap-1"><Icon name="building" :size="13" /> {{ v.org.name }}</span>
                </div>
                <p class="mt-2 text-sm text-stone-600"><span class="text-stone-400">Танилцуулга:</span> {{ v.intro }}</p>
                <p class="mt-1 text-sm text-stone-600"><span class="text-stone-400">Шалтгаан:</span> {{ v.reason }}</p>
                <div class="mt-3">
                    <button
                        v-if="v.status === 'new'"
                        class="act bg-pine-100 text-pine-800 hover:bg-pine-200"
                        @click="setVolStatus(v, 'replied')"
                    >Холбогдсон болгох</button>
                    <button v-else class="act bg-stone-100 text-stone-600 hover:bg-stone-200" @click="setVolStatus(v, 'new')">Шинэ болгох</button>
                </div>
            </div>
        </div>

        <!-- Хариу бичих -->
        <Modal :show="!!detail" title="Санал хүсэлтэд хариу өгөх" @close="detail = null">
            <div v-if="detail">
                <p class="rounded-xl bg-stone-50 p-3 text-sm text-stone-600">{{ detail.message }}</p>
                <textarea v-model="detail.reply" rows="4" class="input mt-3 w-full" placeholder="Хариу (тэмдэглэл)..."></textarea>
                <p class="mt-2 text-xs text-stone-400">Хариу нь дотоод тэмдэглэл бөгөөд статусыг «Хариу өгсөн» болгоно. Хэрэглэгч рүү и-мэйлээр {{ detail.email }} хаягаар холбогдоно уу.</p>
            </div>
            <template #actions>
                <button class="mr-2 rounded-full px-4 py-2 text-sm text-stone-500 hover:bg-stone-100" @click="detail = null">Болих</button>
                <button class="btn-primary" @click="saveReply">Хадгалах</button>
            </template>
        </Modal>
    </div>
</template>

<style scoped>
@reference '../../../css/app.css';

.input { @apply rounded-xl border border-stone-200 bg-white px-3.5 py-2 text-sm outline-none transition focus:border-pine-400; }
.btn-primary { @apply rounded-full bg-pine-700 px-5 py-2 text-sm font-semibold text-white transition hover:bg-pine-800; }
.act { @apply rounded-full px-2.5 py-1 text-xs font-semibold transition; }
.tab { @apply rounded-full px-4 py-1.5 text-sm font-medium text-stone-500 transition hover:bg-stone-100; }
.tab.active { @apply bg-pine-700 text-white; }
</style>
