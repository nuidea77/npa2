<script setup>
import { locale, t } from '../i18n';
import { programs } from '../content/programs';
import Icon from '../components/Icon.vue';
import FeedbackInline from '../components/FeedbackInline.vue';

</script>

<template>
    <div>
        <div class="mx-auto max-w-7xl px-4 pt-10">
            <h1 class="text-2xl font-extrabold uppercase tracking-wide text-stone-900 md:text-[26px]">{{ t('nav.programs') }}</h1>

            <div class="mt-8 grid gap-5 pb-14 sm:grid-cols-2 lg:grid-cols-4">
                <router-link
                    v-for="p in programs"
                    :key="p.slug"
                    :to="p.path"
                    class="group relative block h-80 overflow-hidden rounded-xl bg-pine-900"
                >
                    <img :src="p.image" :alt="t(p.key)" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105" />
                    <div class="absolute inset-0 bg-gradient-to-t from-pine-950/95 via-pine-900/35 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 p-4">
                        <h3 class="text-lg font-bold leading-snug text-white">{{ t(p.key) }}</h3>
                        <p class="mt-1.5 line-clamp-2 text-[13px] leading-relaxed text-white/70">{{ p.short[locale] || p.short.mn }}</p>
                        <span class="mt-3 inline-flex items-center gap-1.5 text-sm font-semibold text-white/90 transition group-hover:gap-2.5">
                            {{ t('common.detail') }} <Icon name="arrow-right" :size="15" />
                        </span>
                    </div>
                </router-link>
            </div>
        </div>

        <FeedbackInline />
    </div>
</template>
