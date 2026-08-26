<script setup>
import { computed } from 'vue';
import { t } from '../i18n';
import { useAuthStore } from '../stores/auth';
import { programs } from '../content/programs';

const auth = useAuthStore();
const contact = computed(() => auth.settings?.contact ?? {
    facebook: 'https://www.facebook.com/NationalParkAcademy',
    instagram: 'https://www.instagram.com/nationalparkacademy/',
    phone: '+976-70100426',
    email: 'info@mongolec.org',
    address: 'Монгол Экологи Төв, Далай Тауэр, 602 тоот, ЮНЕСКО гудамж-31, Сүхбаатар дүүрэг, 1-р хороо, Ш/Х-682, Улаанбаатар-14220',
});
</script>

<template>
    <footer class="bg-pine-950 text-pine-100">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 md:grid-cols-3">
            <div>
                <div class="flex items-center gap-2.5">
                    <img src="/images/logo.svg" alt="NPA" class="h-11 w-11" />
                    <div class="text-lg font-bold text-white">National Park Academy</div>
                </div>
                <p class="mt-4 text-sm leading-relaxed text-pine-200">
                    {{ t('home.heroSubtitle') }}
                </p>
                <div class="mt-4 flex gap-3">
                    <a :href="contact.facebook" target="_blank" rel="noopener" class="social" title="Facebook">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.23.2 2.23.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0 0 22 12z"/></svg>
                    </a>
                    <a :href="contact.instagram" target="_blank" rel="noopener" class="social" title="Instagram">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41a3.72 3.72 0 0 1-1.38-.9 3.72 3.72 0 0 1-.9-1.38c-.16-.42-.36-1.06-.41-2.23-.06-1.27-.07-1.65-.07-4.85s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41 1.27-.06 1.65-.07 4.85-.07M12 0C8.74 0 8.33.01 7.05.07 5.78.13 4.9.33 4.14.63a5.88 5.88 0 0 0-2.13 1.38A5.88 5.88 0 0 0 .63 4.14C.33 4.9.13 5.78.07 7.05.01 8.33 0 8.74 0 12s.01 3.67.07 4.95c.06 1.27.26 2.15.56 2.91.31.8.72 1.47 1.38 2.13a5.88 5.88 0 0 0 2.13 1.38c.76.3 1.64.5 2.91.56C8.33 23.99 8.74 24 12 24s3.67-.01 4.95-.07c1.27-.06 2.15-.26 2.91-.56a5.88 5.88 0 0 0 2.13-1.38 5.88 5.88 0 0 0 1.38-2.13c.3-.76.5-1.64.56-2.91.06-1.28.07-1.69.07-4.95s-.01-3.67-.07-4.95c-.06-1.27-.26-2.15-.56-2.91a5.88 5.88 0 0 0-1.38-2.13A5.88 5.88 0 0 0 19.86.63c-.76-.3-1.64-.5-2.91-.56C15.67.01 15.26 0 12 0zm0 5.84A6.16 6.16 0 1 0 18.16 12 6.16 6.16 0 0 0 12 5.84zm0 10.15A3.99 3.99 0 1 1 16 12a3.99 3.99 0 0 1-4 3.99zM19.85 5.6a1.44 1.44 0 1 1-2.88 0 1.44 1.44 0 0 1 2.88 0z"/></svg>
                    </a>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-bold uppercase tracking-wide text-white">{{ t('nav.programs') }}</h3>
                <ul class="mt-4 grid gap-2 text-sm">
                    <li v-for="p in programs.slice(0, 6)" :key="p.slug">
                        <router-link :to="p.path" class="text-pine-200 transition hover:text-white">{{ t(p.key) }}</router-link>
                    </li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-bold uppercase tracking-wide text-white">{{ t('footer.contact') }}</h3>
                <ul class="mt-4 grid gap-2.5 text-sm text-pine-200">
                    <li><span class="font-semibold text-white">{{ t('footer.phone') }}:</span> {{ contact.phone }}</li>
                    <li><span class="font-semibold text-white">{{ t('footer.email') }}:</span> <a :href="'mailto:' + contact.email" class="underline hover:text-white">{{ contact.email }}</a></li>
                    <li><span class="font-semibold text-white">{{ t('footer.address') }}:</span> {{ contact.address }}</li>
                </ul>
                <router-link
                    to="/help#feedback"
                    class="mt-4 inline-block rounded-full bg-sand-400 px-4 py-2 text-sm font-semibold text-pine-950 transition hover:bg-sand-300"
                >{{ t('nav.feedback') }}</router-link>
            </div>
        </div>
        <div class="border-t border-pine-800/60 py-4 text-center text-xs text-pine-300">
            © {{ new Date().getFullYear() }} National Park Academy — Монгол Экологи Төв. {{ t('footer.rights') }}
        </div>
    </footer>
</template>

<style scoped>
@reference '../../css/app.css';

.social {
    @apply flex h-9 w-9 items-center justify-center rounded-full bg-pine-800 text-pine-100 transition hover:bg-pine-700 hover:text-white;
}
</style>
