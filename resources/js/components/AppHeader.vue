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
    <header class="sticky top-0 z-40 bg-pine-700 text-white">
        <div class="mx-auto flex h-16 max-w-7xl items-center gap-4 px-4">
            <!-- Лого -->
            <router-link to="/" class="group shrink-0" @click="closeAll">
                <Logo img-class="h-9 w-auto transition-transform group-hover:scale-105" />
            </router-link>

            <!-- Үндсэн цэс — баруун талд (Figma) -->
            <nav class="ml-auto hidden items-center gap-1 lg:flex">
                <router-link v-if="show('about')" class="navlink" to="/about" @click="closeAll">{{ t('nav.about') }}</router-link>

                <!-- Хөтөлбөрүүд -->
                <div v-if="show('programs')" class="relative">
                    <button class="navlink flex items-center gap-1" @click="toggle('programs')">
                        {{ t('nav.programs') }}
                        <svg class="h-3.5 w-3.5 opacity-80 transition-transform" :class="{ 'rotate-180': openMenu === 'programs' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
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

                <!-- ТХГ -->
                <div v-if="show('parks')" class="relative">
                    <button class="navlink flex items-center gap-1" @click="toggle('parks')">
                        ТХГ
                        <svg class="h-3.5 w-3.5 opacity-80 transition-transform" :class="{ 'rotate-180': openMenu === 'parks' }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div v-if="openMenu === 'parks'" class="dropdown right-0 left-auto w-72">
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

            <div class="flex items-center gap-2" :class="{ 'ml-auto lg:ml-0': true }">
                <!-- Хэл сонгох — дугуй товч (Figma) -->
                <button
                    class="flex h-10 min-w-10 items-center justify-center rounded-full border border-white/50 px-2 text-[11px] font-bold tracking-wide text-white transition hover:bg-white hover:text-pine-800"
                    @click="switchLang"
                >{{ locale === 'mn' ? 'ENG' : 'МОН' }}</button>

                <!-- Хэрэглэгч -->
                <div class="relative hidden lg:block">
                    <button
                        v-if="auth.isLoggedIn"
                        class="flex h-10 w-10 items-center justify-center rounded-full border border-white/50 text-white transition hover:bg-white hover:text-pine-800"
                        :title="auth.user.name"
                        @click="toggle('user')"
                    >
                        <img v-if="auth.user.photo" :src="auth.user.photo" class="h-full w-full rounded-full object-cover" alt="" />
                        <Icon v-else name="user" :size="17" />
                    </button>
                    <div v-if="openMenu === 'user' && auth.isLoggedIn" class="dropdown right-0 left-auto w-52">
                        <router-link class="dropitem" to="/dashboard" @click="closeAll">{{ t('nav.dashboard') }}</router-link>
                        <router-link v-if="auth.isAdmin" class="dropitem" to="/admin" @click="closeAll">{{ t('nav.admin') }}</router-link>
                        <button class="dropitem w-full text-left text-red-700" @click="logout">{{ t('nav.logout') }}</button>
                    </div>
                </div>

                <!-- Mobile товч -->
                <button class="flex h-10 w-10 items-center justify-center rounded-full border border-white/50 text-white lg:hidden" @click="mobileOpen = !mobileOpen">
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
                <router-link v-else class="moblink font-bold" to="/login" @click="closeAll">{{ t('nav.login') }}</router-link>
            </div>
        </div>
    </header>
</template>

<style scoped>
@reference '../../css/app.css';

.navlink {
    @apply rounded-lg px-3.5 py-2 text-[15px] font-medium text-white/90 transition hover:bg-white/10 hover:text-white;
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
