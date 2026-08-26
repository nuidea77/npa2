<script setup>
import { t } from '../i18n';
import Icon from '../components/Icon.vue';
</script>

<template>
    <div class="mx-auto flex max-w-xl flex-col items-center px-4 py-24 text-center">
        <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-pine-100 text-pine-800"><Icon name="mountain" :size="40" /></div>
        <h1 class="mt-4 text-3xl font-bold text-pine-900">404</h1>
        <p class="mt-2 text-stone-500">Хуудас олдсонгүй. / Page not found.</p>
        <router-link to="/" class="mt-6 rounded-xl bg-pine-700 px-6 py-2.5 font-semibold text-white transition hover:bg-pine-800">
            {{ t('common.back') }} →
        </router-link>
    </div>
</template>
