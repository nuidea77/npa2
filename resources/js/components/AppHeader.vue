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
    <header class="sticky top-0 z-40 bg-pine-700 text-white shadow-md">
        <div class="mx-auto flex h-[72px] max-w-7xl items-center gap-4 px-4">
            <!-- Лого -->
            <router-link to="/" class="shrink-0 text-white" @click="closeAll">
                <Logo emblem-class="h-12 w-auto" text-class="text-[13px]" />
            </router-link>

            <!-- Үндсэн цэс (desktop) -->
            <nav class="ml-4 hidden flex-1 items-center gap-0.5 lg:flex">
                <!-- Бидний тухай -->
                <div v-if="show('about')" class="relative">
                    <button class="navlink" @click="toggle('about')">
                        {{ t('nav.about') }} <span class="text-[10px] opacity-70">▾</span>
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
                        {{ t('nav.programs') }} <span class="text-[10px] opacity-70">▾</span>
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

                <!-- ТХГ-ууд -->
                <div v-if="show('parks')" class="relative">
                    <button class="navlink" @click="toggle('parks')">
                        {{ t('nav.parks') }} <span class="text-[10px] opacity-70">▾</span>
                    </button>
                    <div v-if="openMenu === 'parks'" class="dropdown w-72">
                        <router-link class="dropitem" to="/parks" @click="closeAll">{{ t('nav.parks.all') }}</router-link>
                        <div class="my-1 border-t border-stone-100"></div>
                        <!-- Шаардлагын дагуу Нэвтрэх нь ТХГ цэсний доод хэсэгт байрлана -->
                        <router-link v-if="!auth.isLoggedIn" class="dropitem flex items-center gap-2 font-semibold text-pine-700" to="/login" @click="closeAll">
                            <Icon name="key" :size="16" /> {{ t('nav.login') }}
                        </router-link>
                        <router-link v-else class="dropitem flex items-center gap-2 font-semibold text-pine-700" to="/dashboard" @click="closeAll">
                            <Icon name="user" :size="16" /> {{ t('nav.dashboard') }}
                        </router-link>
                    </div>
                </div>

                <router-link v-if="show('news')" class="navlink" to="/news" @click="closeAll">{{ t('nav.news') }}</router-link>
                <router-link v-if="show('help')" class="navlink" to="/help" @click="closeAll">{{ t('nav.help') }}</router-link>
            </nav>

            <div class="ml-auto flex items-center gap-2.5">
                <!-- Хэл сонгох -->
                <div class="flex overflow-hidden rounded-lg border border-white/30 text-xs font-bold">
                    <button
                        class="px-2.5 py-1.5 transition"
                        :class="locale === 'mn' ? 'bg-white text-pine-800' : 'text-white/80 hover:bg-white/10'"
                        @click="setLocale('mn')"
                    >MN</button>
                    <button
                        class="px-2.5 py-1.5 transition"
                        :class="locale === 'en' ? 'bg-white text-pine-800' : 'text-white/80 hover:bg-white/10'"
                        @click="setLocale('en')"
                    >EN</button>
                </div>

                <!-- Хэрэглэгч -->
                <div class="relative hidden lg:block">
                    <button
                        v-if="auth.isLoggedIn"
                        class="flex items-center gap-2 rounded-lg border border-white/30 px-3 py-1.5 text-sm font-semibold text-white transition hover:bg-white/10"
                        @click="toggle('user')"
                    >
                        <Icon name="user" :size="16" />
                        <span class="max-w-32 truncate">{{ auth.user.first_name || auth.user.name }}</span>
                        <span class="text-[10px] opacity-70">▾</span>
                    </button>
                    <router-link
                        v-else
                        to="/login"
                        class="rounded-lg bg-white px-4 py-2 text-sm font-bold text-pine-800 transition hover:bg-sand-100"
                        @click="closeAll"
                    >{{ t('nav.login') }}</router-link>

                    <div v-if="openMenu === 'user' && auth.isLoggedIn" class="dropdown right-0 left-auto w-52">
                        <router-link class="dropitem" to="/dashboard" @click="closeAll">{{ t('nav.dashboard') }}</router-link>
                        <router-link v-if="auth.isAdmin" class="dropitem" to="/admin" @click="closeAll">{{ t('nav.admin') }}</router-link>
                        <button class="dropitem w-full text-left text-red-700" @click="logout">{{ t('nav.logout') }}</button>
                    </div>
                </div>

                <!-- Mobile товч -->
                <button class="rounded-lg border border-white/30 p-2 text-white lg:hidden" @click="mobileOpen = !mobileOpen">
                    <Icon :name="mobileOpen ? 'x' : 'menu'" :size="20" />
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
                <router-link v-else class="moblink font-bold" to="/login" @click="closeAll">{{ t('nav.login') }}</router-link>
            </div>
        </div>
    </header>
</template>

<style scoped>
@reference '../../css/app.css';

.navlink {
    @apply rounded-lg px-3 py-2 text-sm font-semibold text-white/90 transition hover:bg-white/10 hover:text-white;
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
