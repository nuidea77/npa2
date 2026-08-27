<script setup>
import { computed } from 'vue';
import { locale, t } from '../i18n';
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

function openAssistant() {
    window.dispatchEvent(new CustomEvent('npa-open-chat'));
}
</script>

<template>
    <footer class="bg-pine-700 text-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-10 px-4 py-12 lg:flex-row lg:items-start lg:justify-between">
            <!-- Брэнд -->
            <div class="max-w-sm">
                <img src="/images/logo-square.png" alt="National Park Academy" class="h-24 w-auto" />
                <div class="mt-4 text-lg font-extrabold uppercase tracking-wide">National Park Academy</div>
                <p class="mt-1 text-sm text-white/70">{{ t('about.tagline') }}</p>
                <div class="mt-5 flex gap-2.5">
                    <a :href="contact.facebook" target="_blank" rel="noopener" class="social" title="Facebook">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.23.2 2.23.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0 0 22 12z"/></svg>
                    </a>
                    <a :href="contact.instagram" target="_blank" rel="noopener" class="social" title="Instagram">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r="0.9" fill="currentColor" stroke="none"/></svg>
                    </a>
                </div>
            </div>

            <!-- Холбоо барих баганууд (Figma) -->
            <div class="grid gap-8 sm:grid-cols-3 lg:max-w-2xl lg:flex-1">
                <div>
                    <div class="flex items-center gap-2 text-sm font-bold">
                        <Icon name="phone" :size="16" /> {{ locale === 'en' ? 'Contact' : 'Холбогдох' }}
                    </div>
                    <div class="mt-2.5 text-sm text-white/75">{{ contact.phone }}</div>
                </div>
                <div>
                    <div class="flex items-center gap-2 text-sm font-bold">
                        <Icon name="mail" :size="16" /> {{ locale === 'en' ? 'Email' : 'Имэйл хаяг' }}
                    </div>
                    <a :href="'mailto:' + contact.email" class="mt-2.5 block text-sm text-white/75 transition hover:text-white">{{ contact.email }}</a>
                </div>
                <div>
                    <div class="flex items-center gap-2 text-sm font-bold">
                        <Icon name="map-pin" :size="16" /> {{ locale === 'en' ? 'Address' : 'Байршил' }}
                    </div>
                    <div class="mt-2.5 text-sm leading-relaxed text-white/75">{{ contact.address }}</div>
                    <button class="mt-4 flex items-center gap-2 text-sm font-semibold text-white/90 transition hover:text-white" @click="openAssistant">
                        <span class="flex h-5 w-5 items-center justify-center rounded-full border border-white/60 text-[11px]">?</span>
                        {{ t('footer.assistantBtn') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Доод зурвас -->
        <div class="border-t border-white/15">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-2 px-4 py-4 text-[13px] text-white/60">
                <div>National Park Academy | All right reserved © {{ new Date().getFullYear() }}</div>
                <div>Монгол Экологи Төв</div>
            </div>
        </div>
    </footer>
</template>

<style scoped>
@reference '../../css/app.css';

.social {
    @apply flex h-9 w-9 items-center justify-center rounded-full border border-white/40 text-white transition hover:bg-white hover:text-pine-800;
}
</style>
