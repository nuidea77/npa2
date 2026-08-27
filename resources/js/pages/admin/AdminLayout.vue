<script setup>
import { onMounted, ref } from 'vue';
import axios from '../../bootstrap';
import Icon from '../../components/Icon.vue';

const stats = ref(null);

const nav = [
    { to: '/admin', icon: 'home', label: 'Нүүр', exact: true },
    { to: '/admin/users', icon: 'users', label: 'Хэрэглэгчид', badge: 'pending_users' },
    { to: '/admin/stamps', icon: 'award', label: 'NPA тамга' },
    { to: '/admin/events', icon: 'calendar', label: 'Сургалт, арга хэмжээ' },
    { to: '/admin/inbox', icon: 'inbox', label: 'Санал хүсэлт', badge: 'unanswered_feedback' },
    { to: '/admin/news', icon: 'news', label: 'Мэдээ' },
    { to: '/admin/jobs', icon: 'briefcase', label: 'Ажлын байр' },
    { to: '/admin/parks', icon: 'mountain', label: 'ТХГ-ууд' },
    { to: '/admin/orgs', icon: 'building', label: 'Хамгаалалтын захиргаад' },
    { to: '/admin/trainings', icon: 'graduation', label: 'Сургалтын материал' },
    { to: '/admin/faq', icon: 'chat', label: 'Түгээмэл асуулт' },
    { to: '/admin/settings', icon: 'settings', label: 'Тохиргоо' },
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
                        <span class="flex items-center gap-2.5"><Icon :name="n.icon" :size="17" class="shrink-0 text-pine-600" /> {{ n.label }}</span>
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
                    class="flex shrink-0 items-center gap-1.5 rounded-full border border-stone-200 bg-white px-3.5 py-1.5 text-xs font-medium text-stone-600"
                ><Icon :name="n.icon" :size="14" class="text-pine-600" /> {{ n.label }}</router-link>
            </div>
            <router-view />
        </div>
    </div>
</template>
