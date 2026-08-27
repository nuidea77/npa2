<script setup>
import { onMounted, ref } from 'vue';
import axios from '../../bootstrap';
import Icon from '../../components/Icon.vue';

const stats = ref(null);
const notifications = ref([]);
const outbox = ref([]);
const tab = ref('notifications');

const kinds = {
    user_register: { icon: 'user', label: 'Бүртгэл' },
    volunteer: { icon: 'sprout', label: 'Сайн дурын' },
    feedback: { icon: 'chat', label: 'Санал хүсэлт' },
    event_reg: { icon: 'calendar', label: 'Арга хэмжээ' },
    password_reset: { icon: 'key', label: 'Нууц үг' },
};

async function load() {
    const [s, n, o] = await Promise.all([
        axios.get('/api/admin/stats'),
        axios.get('/api/admin/notifications'),
        axios.get('/api/admin/outbox'),
    ]);
    stats.value = s.data;
    notifications.value = n.data.notifications;
    outbox.value = o.data.outbox;
}

async function markRead(id) {
    await axios.post(`/api/admin/notifications/${id}/read`);
    await load();
}

async function markAll() {
    await axios.post('/api/admin/notifications/read-all');
    await load();
}

onMounted(load);
</script>

<template>
    <div>
        <h1 class="text-xl font-bold text-pine-900">Админ панел — Нүүр</h1>

        <!-- Тоон үзүүлэлт -->
        <div v-if="stats" class="mt-5 grid grid-cols-2 gap-3 md:grid-cols-4">
            <router-link to="/admin/users?status=pending" class="stat border-amber-200 bg-amber-50">
                <div class="text-2xl font-extrabold text-amber-700">{{ stats.pending_users }}</div>
                <div class="text-xs text-amber-800">Хүлээгдэж буй бүртгэл</div>
            </router-link>
            <router-link to="/admin/users" class="stat border-pine-100 bg-white">
                <div class="text-2xl font-extrabold text-pine-700">{{ stats.active_users }}</div>
                <div class="text-xs text-stone-500">Идэвхтэй хэрэглэгч</div>
            </router-link>
            <router-link to="/admin/inbox" class="stat border-pine-100 bg-white">
                <div class="text-2xl font-extrabold text-pine-700">{{ stats.unanswered_feedback }}</div>
                <div class="text-xs text-stone-500">Хариу өгөөгүй санал хүсэлт</div>
            </router-link>
            <router-link to="/admin/inbox?tab=volunteers" class="stat border-pine-100 bg-white">
                <div class="text-2xl font-extrabold text-pine-700">{{ stats.new_volunteers }}</div>
                <div class="text-xs text-stone-500">Шинэ сайн дурын хүсэлт</div>
            </router-link>
        </div>

        <!-- Мэдэгдэл / И-мэйл лог -->
        <div class="mt-8 rounded-2xl border border-stone-100 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-stone-100 px-5 py-3">
                <div class="flex gap-2">
                    <button class="tab" :class="{ active: tab === 'notifications' }" @click="tab = 'notifications'">
                        Мэдэгдэл <span v-if="stats?.unread_notifications" class="ml-1 rounded-full bg-red-100 px-1.5 text-xs font-bold text-red-700">{{ stats.unread_notifications }}</span>
                    </button>
                    <button class="tab" :class="{ active: tab === 'outbox' }" @click="tab = 'outbox'">И-мэйл лог</button>
                </div>
                <button v-if="tab === 'notifications'" class="text-xs font-medium text-pine-600 hover:text-pine-800" @click="markAll">Бүгдийг уншсан болгох</button>
            </div>

            <div v-if="tab === 'notifications'" class="divide-y divide-stone-50">
                <div v-if="!notifications.length" class="p-5 text-sm text-stone-400">Мэдэгдэл алга.</div>
                <div v-for="n in notifications" :key="n.id" class="flex items-center gap-3 px-5 py-3" :class="{ 'bg-pine-50/50': !n.read }">
                    <span class="flex shrink-0 items-center gap-1.5 text-xs text-stone-500">
                        <Icon :name="kinds[n.kind]?.icon ?? 'bell'" :size="14" class="text-pine-600" /> {{ kinds[n.kind]?.label ?? n.kind }}
                    </span>
                    <span class="flex-1 text-sm text-stone-700">{{ n.text }}</span>
                    <span class="shrink-0 text-xs text-stone-400">{{ n.created_at?.slice(0, 16).replace('T', ' ') }}</span>
                    <button v-if="!n.read" class="shrink-0 text-xs font-medium text-pine-600 hover:underline" @click="markRead(n.id)">Уншсан</button>
                </div>
            </div>

            <div v-else class="divide-y divide-stone-50">
                <div v-if="!outbox.length" class="p-5 text-sm text-stone-400">И-мэйл лог хоосон.</div>
                <div v-for="m in outbox" :key="m.id" class="px-5 py-3">
                    <div class="flex justify-between gap-3 text-xs text-stone-400">
                        <span>→ {{ m.to }}</span>
                        <span>{{ m.created_at?.slice(0, 16).replace('T', ' ') }}</span>
                    </div>
                    <div class="mt-0.5 text-sm font-medium text-stone-700">{{ m.subject }}</div>
                    <div class="mt-0.5 text-xs text-stone-500">{{ m.body }}</div>
                </div>
                <p class="px-5 py-3 text-xs text-stone-400">
                    SMTP тохируулаагүй тул и-мэйлүүд энд лог хэлбэрээр хадгалагдаж байна. .env дээр MAIL_* тохиргоог хийснээр бодит илгээлт ажиллана.
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
@reference '../../../css/app.css';

.stat { @apply rounded-2xl border p-4 shadow-sm transition hover:shadow; }
.tab { @apply rounded-full px-3.5 py-1.5 text-sm font-medium text-stone-500 transition hover:bg-stone-100; }
.tab.active { @apply bg-pine-700 text-white; }
</style>
