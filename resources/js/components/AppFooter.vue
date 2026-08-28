<script setup>
import { computed } from 'vue';
import { t } from '../i18n';
import { useAuthStore } from '../stores/auth';
import Icon from './Icon.vue';

const auth = useAuthStore();
const contact = computed(() => auth.settings?.contact ?? {
    facebook: 'https://www.facebook.com/NationalParkAcademy',
    instagram: 'https://www.instagram.com/nationalparkacademy/',
    phone: '+976-70100426',
    email: 'info@mongolec.org',
    address: 'Монгол Экологи Төв, Далай Тауэр, 602 тоот, ЮНЕСКО гудамж-31, Сүхбаатар дүүрэг, 1-р хороо, Ш/Х-682, Улаанбаатар-14220',
});

const quickLinks = [
    { to: '/about', key: 'nav.about' },
    { to: '/programs', key: 'nav.programs' },
    { to: '/about#team', key: 'nav.about.team' },
    { to: '/news', key: 'nav.news' },
];

function openAssistant() {
    window.dispatchEvent(new CustomEvent('npa-open-chat'));
}
</script>

<template>
    <footer class="bg-pine-700 text-white">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="mb-12 grid gap-12 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Брэнд -->
                <div>
                    <div class="mb-4">
                        <img src="/images/logo-white.png" alt="National Park Academy Logo" class="h-10 w-auto" />
                    </div>
                    <p class="mb-6 text-sm leading-relaxed text-white/80">{{ t('footer.tagline') }}</p>
                    <div class="flex gap-3">
                        <a :href="contact.facebook" target="_blank" rel="noopener" class="social" title="Facebook">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.23.2 2.23.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0 0 22 12z"/></svg>
                        </a>
                        <a :href="contact.instagram" target="_blank" rel="noopener" class="social" title="Instagram">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r="0.9" fill="currentColor" stroke="none"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Шуурхай холбоос -->
                <div>
                    <h3 class="mb-4 text-base font-semibold">{{ t('footer.quick') }}</h3>
                    <ul class="space-y-3">
                        <li v-for="l in quickLinks" :key="l.to">
                            <router-link :to="l.to" class="text-sm text-white/80 transition-colors hover:text-white">{{ t(l.key) }}</router-link>
                        </li>
                    </ul>
                </div>

                <!-- Холбоо барих -->
                <div>
                    <h3 class="mb-4 text-base font-semibold">{{ t('footer.contact') }}</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <Icon name="mail" :size="18" class="mt-0.5 shrink-0" />
                            <a :href="'mailto:' + contact.email" class="text-sm text-white/80 transition-colors hover:text-white">{{ contact.email }}</a>
                        </li>
                        <li class="flex items-start gap-3">
                            <Icon name="phone" :size="18" class="mt-0.5 shrink-0" />
                            <a :href="'tel:' + contact.phone.replace(/[^+0-9]/g, '')" class="text-sm text-white/80 transition-colors hover:text-white">{{ contact.phone }}</a>
                        </li>
                        <li class="flex items-start gap-3">
                            <Icon name="map-pin" :size="18" class="mt-0.5 shrink-0" />
                            <div class="text-sm leading-relaxed text-white/80">{{ contact.address }}</div>
                        </li>
                    </ul>
                </div>

                <!-- Дэмжих -->
                <div>
                    <h3 class="mb-4 text-base font-semibold">{{ t('footer.support') }}</h3>
                    <p class="mb-4 text-sm leading-relaxed text-white/80">{{ t('footer.supportText') }}</p>
                    <router-link
                        to="/about"
                        class="inline-flex items-center justify-center rounded-lg bg-white px-6 py-2 text-sm font-medium text-pine-700 transition-colors hover:bg-white/90"
                    >
                        {{ t('footer.aboutBtn') }}
                    </router-link>
                    <button class="mt-4 flex items-center gap-2 text-sm font-semibold text-white/80 transition-colors hover:text-white" @click="openAssistant">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full border border-white/60 text-[11px]">?</span>
                        {{ t('footer.assistantBtn') }}
                    </button>
                </div>
            </div>

            <!-- Доод зурвас -->
            <div class="border-t border-white/20 pt-8">
                <div class="flex items-center justify-center text-sm text-white/80">
                    <p>© {{ new Date().getFullYear() }} National Park Academy. {{ t('footer.rights') }}</p>
                </div>
            </div>
        </div>
    </footer>
</template>

<style scoped>
@reference '../../css/app.css';

.social {
    @apply flex h-9 w-9 items-center justify-center rounded-full bg-white/10 text-white transition-colors hover:bg-white/20;
}
</style>
