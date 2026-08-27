<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import Icon from './Icon.vue';

/**
 * Зургийн carousel — сумтай, цэгэн заагчтай (Figma загварын дагуу).
 * slides: [{ image, title?, link? }]
 */
const props = defineProps({
    slides: { type: Array, required: true },
    heightClass: { type: String, default: 'h-72 md:h-96' },
    auto: { type: Boolean, default: false },
});

const idx = ref(0);
let timer = null;

function go(i) {
    idx.value = (i + props.slides.length) % props.slides.length;
}

onMounted(() => {
    if (props.auto && props.slides.length > 1) {
        timer = setInterval(() => go(idx.value + 1), 5000);
    }
});
onBeforeUnmount(() => timer && clearInterval(timer));
</script>

<template>
    <div class="group relative overflow-hidden rounded-xl" :class="heightClass">
        <template v-for="(s, i) in slides" :key="i">
            <img
                v-show="i === idx"
                :src="s.image"
                :alt="s.title ?? ''"
                class="h-full w-full object-cover"
            />
        </template>

        <slot :slide="slides[idx]" :index="idx" />

        <!-- Сумнууд -->
        <template v-if="slides.length > 1">
            <button class="nav left-3" @click="go(idx - 1)"><Icon name="arrow-right" :size="16" class="rotate-180" /></button>
            <button class="nav right-3" @click="go(idx + 1)"><Icon name="arrow-right" :size="16" /></button>

            <!-- Цэгүүд -->
            <div class="absolute bottom-3 left-1/2 flex -translate-x-1/2 gap-1.5">
                <button
                    v-for="(s, i) in slides"
                    :key="'d' + i"
                    class="h-2 w-2 rounded-full transition"
                    :class="i === idx ? 'bg-white' : 'bg-white/40 hover:bg-white/70'"
                    @click="go(i)"
                ></button>
            </div>
        </template>
    </div>
</template>

<style scoped>
@reference '../../css/app.css';

.nav {
    @apply absolute top-1/2 flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full bg-black/35 text-white opacity-0 backdrop-blur-sm transition hover:bg-black/55 group-hover:opacity-100;
}
</style>
