<script setup>
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';
import { locale, setLocale, t } from '../i18n';
import { useAuthStore } from '../stores/auth';
import { programs } from '../content/programs';

const auth = useAuthStore();
const router = useRouter();
const mobileOpen = ref(false);
const openMenu = ref(null);

const menu = computed(() => auth.settings?.menu ?? {});
const show = (key) => menu.value[key] !== false;

function toggle(name) {
    openMenu.value = openMenu.value === name ? null : name;
}

function closeAll() {
    openMenu.value = null;
    mobileOpen.value = false;
}

async function logout() {
    await auth.logout();
    closeAll();
    router.push('/');
}
</script>

<template>
    <header class="sticky top-0 z-40 border-b border-pine-100 bg-white/95 shadow-sm backdrop-blur">
        <div class="mx-auto flex h-16 max-w-7xl items-center gap-4 px-4">
            <!-- Лого -->
            <router-link to="/" class="flex items-center gap-2.5" @click="closeAll">
                <img src="/images/logo.svg" alt="NPA" class="h-10 w-10" />
                <span class="leading-tight">
                    <span class="block text-sm font-bold text-pine-800">National Park</span>
                    <span class="block text-sm font-bold text-pine-600">Academy</span>
                </span>
            </router-link>

            <!-- Үндсэн цэс (desktop) -->
            <nav class="ml-4 hidden flex-1 items-center gap-1 lg:flex">
                <!-- Бидний тухай -->
                <div v-if="show('about')" class="relative">
                    <button class="navlink" @click="toggle('about')">
                        {{ t('nav.about') }} <span class="text-xs">▾</span>
                    </button>
                    <div v-if="openMenu === 'about'" class="dropdown">
                        <router-link class="dropitem" to="/about#mission" @click="closeAll">{{ t('nav.about.mission') }}</router-link>
                        <router-link class="dropitem" to="/about#about" @click="closeAll">{{ t('nav.about.about') }}</router-link>
                        <router-link class="dropitem" to="/about#timeline" @click="closeAll">{{ t('nav.about.timeline') }}</router-link>
                        <router-link class="dropitem" to="/about#team" @click="closeAll">{{ t('nav.about.team') }}</router-link>
                    </div>
                </div>

                <!-- Хөтөлбөрүүд -->
                <div v-if="show('programs')" class="relative">
                    <button class="navlink" @click="toggle('programs')">
                        {{ t('nav.programs') }} <span class="text-xs">▾</span>
                    </button>
                    <div v-if="openMenu === 'programs'" class="dropdown w-96">
                        <router-link
                            v-for="p in programs"
                            :key="p.slug"
                            class="dropitem"
                            :to="p.path"
                            @click="closeAll"
                        >
                            <span class="mr-1.5">{{ p.icon }}</span>{{ t(p.key) }}
                        </router-link>
                    </div>
                </div>

                <!-- ТХГ-ууд -->
                <div v-if="show('parks')" class="relative">
                    <button class="navlink" @click="toggle('parks')">
                        {{ t('nav.parks') }} <span class="text-xs">▾</span>
                    </button>
                    <div v-if="openMenu === 'parks'" class="dropdown w-72">
                        <router-link class="dropitem" to="/parks" @click="closeAll">{{ t('nav.parks.all') }}</router-link>
                        <div class="my-1 border-t border-stone-100"></div>
                        <!-- Шаардлагын дагуу Нэвтрэх нь ТХГ цэсний доод хэсэгт байрлана -->
                        <router-link v-if="!auth.isLoggedIn" class="dropitem font-medium text-pine-700" to="/login" @click="closeAll">
                            🔑 {{ t('nav.login') }}
                        </router-link>
                        <router-link v-else class="dropitem font-medium text-pine-700" to="/dashboard" @click="closeAll">
                            👤 {{ t('nav.dashboard') }}
                        </router-link>
                    </div>
                </div>

                <router-link v-if="show('news')" class="navlink" to="/news" @click="closeAll">{{ t('nav.news') }}</router-link>
                <router-link v-if="show('help')" class="navlink" to="/help" @click="closeAll">{{ t('nav.help') }}</router-link>
            </nav>

            <div class="ml-auto flex items-center gap-2">
                <!-- Санал хүсэлт -->
                <router-link
                    to="/help#feedback"
                    class="hidden rounded-full bg-sand-100 px-3.5 py-1.5 text-sm font-medium text-sand-800 transition hover:bg-sand-200 md:block"
                    @click="closeAll"
                >
                    {{ t('nav.feedback') }}
                </router-link>

                <!-- Хэл сонгох -->
                <div class="flex overflow-hidden rounded-full border border-stone-200 text-xs font-semibold">
                    <button
                        class="px-2.5 py-1.5 transition"
                        :class="locale === 'mn' ? 'bg-pine-700 text-white' : 'bg-white text-stone-500 hover:bg-stone-50'"
                        @click="setLocale('mn')"
                    >MN</button>
                    <button
                        class="px-2.5 py-1.5 transition"
                        :class="locale === 'en' ? 'bg-pine-700 text-white' : 'bg-white text-stone-500 hover:bg-stone-50'"
                        @click="setLocale('en')"
                    >EN</button>
                </div>

                <!-- Хэрэглэгч -->
                <div class="relative hidden lg:block">
                    <button
                        v-if="auth.isLoggedIn"
                        class="flex items-center gap-1.5 rounded-full border border-pine-200 bg-pine-50 px-3 py-1.5 text-sm font-medium text-pine-800"
                        @click="toggle('user')"
                    >
                        <span>{{ auth.user.gender === 'female' ? '👩' : '👨' }}</span>
                        <span class="max-w-32 truncate">{{ auth.user.first_name || auth.user.name }}</span>
                        <span class="text-xs">▾</span>
                    </button>
                    <router-link
                        v-else
                        to="/login"
                        class="rounded-full bg-pine-700 px-4 py-1.5 text-sm font-semibold text-white transition hover:bg-pine-800"
                        @click="closeAll"
                    >{{ t('nav.login') }}</router-link>

                    <div v-if="openMenu === 'user' && auth.isLoggedIn" class="dropdown right-0 left-auto w-52">
                        <router-link class="dropitem" to="/dashboard" @click="closeAll">{{ t('nav.dashboard') }}</router-link>
                        <router-link v-if="auth.isAdmin" class="dropitem" to="/admin" @click="closeAll">{{ t('nav.admin') }}</router-link>
                        <button class="dropitem w-full text-left text-red-600" @click="logout">{{ t('nav.logout') }}</button>
                    </div>
                </div>

                <!-- Mobile товч -->
                <button class="rounded-lg border border-stone-200 p-2 lg:hidden" @click="mobileOpen = !mobileOpen">
                    <svg class="h-5 w-5 text-stone-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile цэс -->
        <div v-if="mobileOpen" class="border-t border-stone-100 bg-white px-4 py-3 lg:hidden">
            <div class="grid gap-1 text-sm">
                <router-link v-if="show('about')" class="moblink" to="/about" @click="closeAll">{{ t('nav.about') }}</router-link>
                <router-link v-if="show('programs')" class="moblink" to="/programs" @click="closeAll">{{ t('nav.programs') }}</router-link>
                <router-link v-if="show('parks')" class="moblink" to="/parks" @click="closeAll">{{ t('nav.parks') }}</router-link>
                <router-link class="moblink" to="/jobs" @click="closeAll">{{ t('nav.jobs') }}</router-link>
                <router-link v-if="show('news')" class="moblink" to="/news" @click="closeAll">{{ t('nav.news') }}</router-link>
                <router-link v-if="show('help')" class="moblink" to="/help" @click="closeAll">{{ t('nav.help') }}</router-link>
                <div class="my-1 border-t border-stone-100"></div>
                <template v-if="auth.isLoggedIn">
                    <router-link class="moblink" to="/dashboard" @click="closeAll">{{ t('nav.dashboard') }}</router-link>
                    <router-link v-if="auth.isAdmin" class="moblink" to="/admin" @click="closeAll">{{ t('nav.admin') }}</router-link>
                    <button class="moblink text-left text-red-600" @click="logout">{{ t('nav.logout') }}</button>
                </template>
                <router-link v-else class="moblink font-semibold text-pine-700" to="/login" @click="closeAll">{{ t('nav.login') }}</router-link>
            </div>
        </div>
    </header>
</template>

<style scoped>
@reference '../../css/app.css';

.navlink {
    @apply rounded-lg px-3 py-2 text-sm font-medium text-stone-700 transition hover:bg-pine-50 hover:text-pine-800;
}
.dropdown {
    @apply absolute left-0 top-full z-50 mt-1 w-64 rounded-xl border border-stone-100 bg-white p-1.5 shadow-lg;
}
.dropitem {
    @apply block rounded-lg px-3 py-2 text-sm text-stone-700 transition hover:bg-pine-50 hover:text-pine-800;
}
.moblink {
    @apply rounded-lg px-3 py-2 font-medium text-stone-700 hover:bg-pine-50;
}
</style>
