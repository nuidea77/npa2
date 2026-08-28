<script setup>
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';
import { locale, setLocale, t } from '../i18n';
import { useAuthStore } from '../stores/auth';
import { programs } from '../content/programs';
import Logo from './Logo.vue';
import Icon from './Icon.vue';

const auth = useAuthStore();
const router = useRouter();
const mobileOpen = ref(false);
const openMenu = ref(null);

const menu = computed(() => auth.settings?.menu ?? {});
const show = (key) => menu.value[key] !== false;

const aboutItems = [
    { to: '/about#mission', key: 'nav.about.mission' },
    { to: '/about#about', key: 'nav.about.about' },
    { to: '/about#timeline', key: 'nav.about.timeline' },
    { to: '/about#team', key: 'nav.about.team' },
];

function toggle(name) {
    openMenu.value = openMenu.value === name ? null : name;
}

function closeAll() {
    openMenu.value = null;
    mobileOpen.value = false;
}

function switchLang() {
    setLocale(locale.value === 'mn' ? 'en' : 'mn');
}

async function logout() {
    await auth.logout();
    closeAll();
    router.push('/');
}
</script>

<template>
    <header class="sticky top-0 z-40 bg-pine-700 text-white transition-all duration-300">
        <div class="mx-auto flex h-16 max-w-7xl items-center gap-4 px-4 sm:px-6 lg:px-8">
            <!-- Лого — hover дээр хөдөлгөөнгүй -->
            <router-link to="/" class="flex shrink-0 items-center gap-3" @click="closeAll">
                <Logo img-class="h-10 w-auto" />
            </router-link>

            <!-- Үндсэн цэс — баруун талд -->
            <nav class="ml-auto hidden items-center gap-1 lg:flex">
                <!-- Бидний тухай -->
                <div v-if="show('about')" class="relative">
                    <button class="navlink flex items-center gap-1" @click="toggle('about')">
                        {{ t('nav.about') }}
                        <svg class="h-3.5 w-3.5 opacity-70 transition-transform" :class="{ 'rotate-180': openMenu === 'about' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div v-if="openMenu === 'about'" class="dropdown w-56">
                        <router-link v-for="i in aboutItems" :key="i.to" class="dropitem" :to="i.to" @click="closeAll">{{ t(i.key) }}</router-link>
                    </div>
                </div>

                <!-- Хөтөлбөрүүд -->
                <div v-if="show('programs')" class="relative">
                    <button class="navlink flex items-center gap-1" @click="toggle('programs')">
                        {{ t('nav.programs') }}
                        <svg class="h-3.5 w-3.5 opacity-70 transition-transform" :class="{ 'rotate-180': openMenu === 'programs' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div v-if="openMenu === 'programs'" class="dropdown w-96">
                        <router-link
                            v-for="p in programs"
                            :key="p.slug"
                            class="dropitem flex items-center gap-2.5"
                            :to="p.path"
                            @click="closeAll"
                        >
                            <Icon :name="p.icon" :size="17" class="shrink-0 text-pine-600" />
                            <span>{{ t(p.key) }}</span>
                        </router-link>
                    </div>
                </div>

                <router-link v-if="show('parks')" class="navlink" to="/parks" @click="closeAll">ТХГ</router-link>
                <router-link v-if="show('news')" class="navlink" to="/news" @click="closeAll">{{ t('nav.news') }}</router-link>
                <router-link v-if="show('help')" class="navlink" to="/help" @click="closeAll">{{ t('nav.help') }}</router-link>
            </nav>

            <div class="flex items-center gap-2.5" :class="{ 'ml-auto lg:ml-0': true }">
                <!-- Хэл сонгох -->
                <button
                    class="rounded-md border border-white/30 px-2.5 py-1.5 text-xs font-bold tracking-wide text-white/90 transition-colors hover:bg-white/10 hover:text-white"
                    @click="switchLang"
                >{{ locale === 'mn' ? 'EN' : 'MN' }}</button>

                <!-- Нэвтрэх / хэрэглэгч -->
                <router-link
                    v-if="!auth.isLoggedIn"
                    to="/login"
                    class="hidden items-center gap-2 rounded-md bg-white px-4 py-2 text-sm font-medium text-pine-700 transition-colors hover:bg-white/90 lg:flex"
                    @click="closeAll"
                >
                    <Icon name="user" :size="16" />
                    <span>{{ t('nav.login') }}</span>
                </router-link>
                <div v-else class="relative hidden lg:block">
                    <button
                        class="flex h-10 w-10 items-center justify-center rounded-full border border-white/40 text-white transition hover:bg-white hover:text-pine-800"
                        :title="auth.user.name"
                        @click="toggle('user')"
                    >
                        <img v-if="auth.user.photo" :src="auth.user.photo" class="h-full w-full rounded-full object-cover" alt="" />
                        <Icon v-else name="user" :size="17" />
                    </button>
                    <div v-if="openMenu === 'user'" class="dropdown right-0 left-auto w-52">
                        <router-link class="dropitem" to="/dashboard" @click="closeAll">{{ t('nav.dashboard') }}</router-link>
                        <router-link v-if="auth.isAdmin" class="dropitem" to="/admin" @click="closeAll">{{ t('nav.admin') }}</router-link>
                        <button class="dropitem w-full text-left text-red-700" @click="logout">{{ t('nav.logout') }}</button>
                    </div>
                </div>

                <!-- Mobile товч -->
                <button class="flex h-10 w-10 items-center justify-center rounded-md border border-white/30 text-white lg:hidden" @click="mobileOpen = !mobileOpen">
                    <Icon :name="mobileOpen ? 'x' : 'menu'" :size="19" />
                </button>
            </div>
        </div>

        <!-- Mobile цэс -->
        <div v-if="mobileOpen" class="border-t border-white/15 bg-pine-700 px-4 py-3 lg:hidden">
            <div class="grid gap-0.5 text-sm">
                <router-link v-if="show('about')" class="moblink" to="/about" @click="closeAll">{{ t('nav.about') }}</router-link>
                <router-link v-if="show('programs')" class="moblink" to="/programs" @click="closeAll">{{ t('nav.programs') }}</router-link>
                <router-link v-if="show('parks')" class="moblink" to="/parks" @click="closeAll">{{ t('nav.parks') }}</router-link>
                <router-link class="moblink" to="/jobs" @click="closeAll">{{ t('nav.jobs') }}</router-link>
                <router-link v-if="show('news')" class="moblink" to="/news" @click="closeAll">{{ t('nav.news') }}</router-link>
                <router-link v-if="show('help')" class="moblink" to="/help" @click="closeAll">{{ t('nav.help') }}</router-link>
                <div class="my-1.5 border-t border-white/15"></div>
                <template v-if="auth.isLoggedIn">
                    <router-link class="moblink" to="/dashboard" @click="closeAll">{{ t('nav.dashboard') }}</router-link>
                    <router-link v-if="auth.isAdmin" class="moblink" to="/admin" @click="closeAll">{{ t('nav.admin') }}</router-link>
                    <button class="moblink text-left text-red-200" @click="logout">{{ t('nav.logout') }}</button>
                </template>
                <router-link
                    v-else
                    class="mt-1 flex items-center justify-center gap-2 rounded-md bg-white px-4 py-2.5 text-sm font-medium text-pine-700"
                    to="/login"
                    @click="closeAll"
                >
                    <Icon name="user" :size="16" /> {{ t('nav.login') }}
                </router-link>
            </div>
        </div>
    </header>
</template>

<style scoped>
@reference '../../css/app.css';

.navlink {
    @apply px-3 py-2 text-sm font-medium text-white/80 transition-colors hover:text-white;
}
.dropdown {
    @apply absolute left-0 top-full z-50 mt-2 w-64 rounded-xl border border-stone-200 bg-white p-1.5 text-stone-700 shadow-lg;
}
.dropitem {
    @apply block rounded-lg px-3 py-2 text-sm text-stone-700 transition hover:bg-pine-50 hover:text-pine-800;
}
.moblink {
    @apply rounded-lg px-3 py-2.5 font-semibold text-white/90 hover:bg-white/10;
}
</style>
