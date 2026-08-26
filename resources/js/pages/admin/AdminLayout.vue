<script setup>
import { onMounted, ref } from 'vue';
import axios from '../../bootstrap';

const stats = ref(null);

const nav = [
    { to: '/admin', label: '🏠 Нүүр', exact: true },
    { to: '/admin/users', label: '👥 Хэрэглэгчид', badge: 'pending_users' },
    { to: '/admin/stamps', label: '🎖️ NPA тамга' },
    { to: '/admin/events', label: '📅 Сургалт, арга хэмжээ' },
    { to: '/admin/inbox', label: '📨 Санал хүсэлт', badge: 'unanswered_feedback' },
    { to: '/admin/news', label: '📰 Мэдээ' },
    { to: '/admin/jobs', label: '💼 Ажлын байр' },
    { to: '/admin/parks', label: '🏞️ ТХГ-ууд' },
    { to: '/admin/orgs', label: '🏢 Хамгаалалтын захиргаад' },
    { to: '/admin/trainings', label: '🎓 Сургалтын материал' },
    { to: '/admin/faq', label: '❓ Түгээмэл асуулт' },
    { to: '/admin/settings', label: '⚙️ Тохиргоо' },
];

onMounted(async () => {
    try {
        const { data } = await axios.get('/api/admin/stats');
        stats.value = data;
    } catch { /* noop */ }
});
</script>

<template>
    <div class="mx-auto flex max-w-7xl gap-6 px-4 py-8">
        <!-- Хажуугийн цэс -->
        <aside class="hidden w-60 shrink-0 md:block">
            <div class="sticky top-24 rounded-2xl border border-stone-100 bg-white p-3 shadow-sm">
                <div class="px-3 py-2 text-xs font-bold uppercase tracking-widest text-stone-400">Админ панел</div>
                <nav class="grid gap-0.5">
                    <router-link
                        v-for="n in nav"
                        :key="n.to"
                        :to="n.to"
                        class="flex items-center justify-between rounded-xl px-3 py-2 text-sm font-medium text-stone-600 transition hover:bg-pine-50 hover:text-pine-800"
                        :class="{ 'bg-pine-50 text-pine-800': n.exact ? $route.path === n.to : $route.path.startsWith(n.to) && n.to !== '/admin' }"
                    >
                        {{ n.label }}
                        <span
                            v-if="n.badge && stats?.[n.badge]"
                            class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-bold text-red-700"
                        >{{ stats[n.badge] }}</span>
                    </router-link>
                </nav>
            </div>
        </aside>

        <!-- Агуулга -->
        <div class="min-w-0 flex-1">
            <!-- Mobile цэс -->
            <div class="mb-4 flex gap-2 overflow-x-auto pb-1 md:hidden">
                <router-link
                    v-for="n in nav"
                    :key="n.to"
                    :to="n.to"
                    class="shrink-0 rounded-full border border-stone-200 bg-white px-3.5 py-1.5 text-xs font-medium text-stone-600"
                >{{ n.label }}</router-link>
            </div>
            <router-view />
        </div>
    </div>
</template>
