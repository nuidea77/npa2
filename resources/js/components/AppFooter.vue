<script setup>
import { computed } from 'vue';
import { locale, t } from '../i18n';
import { useAuthStore } from '../stores/auth';
import Logo from './Logo.vue';
import Icon from './Icon.vue';

const auth = useAuthStore();
const contact = computed(() => auth.settings?.contact ?? {
    facebook: 'https://www.facebook.com/NationalParkAcademy',
    instagram: 'https://www.instagram.com/nationalparkacademy/',
    phone: '+976-70100426',
    email: 'info@mongolec.org',
    address: 'Монгол Экологи Төв, Далай Тауэр, 602 тоот, ЮНЕСКО гудамж-31, Сүхбаатар дүүрэг, 1-р хороо, Ш/Х-682, Улаанбаатар-14220',
});

const quickLinks = computed(() => [
    { to: '/about', label: t('nav.about') },
    { to: '/programs', label: t('nav.programs') },
    { to: '/parks', label: t('nav.parks') },
    { to: '/jobs', label: t('nav.jobs') },
    { to: '/news', label: t('nav.news') },
    { to: '/help', label: t('nav.help') },
]);
</script>

<template>
    <footer class="bg-pine-700 text-white">
        <div class="mx-auto grid max-w-7xl gap-12 px-4 py-14 md:grid-cols-2 lg:grid-cols-4">
            <!-- Лого + тайлбар -->
            <div>
                <Logo emblem-class="h-14 w-auto" text-class="text-sm" />
                <p class="mt-5 text-[15px] leading-relaxed text-white/80">
                    {{ locale === 'en'
                        ? "Empowering conservation leaders through education and hands-on experience in Mongolia's protected areas."
                        : 'Монгол Улсын Тусгай хамгаалалттай газруудын менежментийг сайжруулж, олон улсын сайн туршлагыг нэвтрүүлнэ.' }}
                </p>
                <div class="mt-5 flex gap-3">
                    <a :href="contact.facebook" target="_blank" rel="noopener" class="social" title="Facebook">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.23.2 2.23.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0 0 22 12z"/></svg>
                    </a>
                    <a :href="contact.instagram" target="_blank" rel="noopener" class="social" title="Instagram">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r="0.9" fill="currentColor" stroke="none"/></svg>
                    </a>
                </div>
            </div>

            <!-- Холбоос -->
            <div>
                <h3 class="text-lg font-bold text-white">{{ locale === 'en' ? 'Quick Links' : 'Холбоос' }}</h3>
                <ul class="mt-5 grid gap-3.5 text-[15px]">
                    <li v-for="l in quickLinks" :key="l.to">
                        <router-link :to="l.to" class="text-white/80 transition hover:text-white">{{ l.label }}</router-link>
                    </li>
                </ul>
            </div>

            <!-- Холбоо барих -->
            <div>
                <h3 class="text-lg font-bold text-white">{{ t('footer.contact') }}</h3>
                <ul class="mt-5 grid gap-4 text-[15px] text-white/80">
                    <li class="flex items-start gap-3">
                        <Icon name="mail" :size="20" class="mt-0.5 shrink-0" />
                        <a :href="'mailto:' + contact.email" class="transition hover:text-white">{{ contact.email }}</a>
                    </li>
                    <li class="flex items-start gap-3">
                        <Icon name="phone" :size="20" class="mt-0.5 shrink-0" />
                        <span>{{ contact.phone }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <Icon name="map-pin" :size="20" class="mt-0.5 shrink-0" />
                        <span class="leading-relaxed">{{ contact.address }}</span>
                    </li>
                </ul>
            </div>

            <!-- Санал хүсэлт CTA -->
            <div>
                <h3 class="text-lg font-bold text-white">{{ locale === 'en' ? 'Support National Parks' : 'ТХГ-уудаа дэмжье' }}</h3>
                <p class="mt-5 text-[15px] leading-relaxed text-white/80">
                    {{ locale === 'en'
                        ? "Help us protect Mongolia's natural heritage for future generations."
                        : 'Монгол орны байгалийн өвийг хойч үедээ өвлүүлэхэд бидэнтэй нэгдээрэй.' }}
                </p>
                <router-link
                    to="/help#feedback"
                    class="mt-5 inline-block rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-pine-800 transition hover:bg-sand-100"
                >{{ t('nav.feedback') }}</router-link>
            </div>
        </div>
        <div class="border-t border-white/15 py-5 text-center text-[13px] text-white/60">
            © {{ new Date().getFullYear() }} National Park Academy — Монгол Экологи Төв. {{ t('footer.rights') }}
        </div>
    </footer>
</template>

<style scoped>
@reference '../../css/app.css';

.social {
    @apply flex h-11 w-11 items-center justify-center rounded-full border border-white/40 text-white transition hover:bg-white hover:text-pine-800;
}
</style>
