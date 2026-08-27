<script setup>
/** Жагсаалтын хуудасны толгой — цагаан дэвсгэр, зүүн талд том UPPERCASE гарчиг (Figma). */
defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
});
</script>

<template>
    <section class="border-b border-stone-100 bg-white">
        <div class="mx-auto max-w-7xl px-4 pb-6 pt-10">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <h1 class="text-2xl font-extrabold uppercase tracking-wide text-stone-900 md:text-3xl">{{ title }}</h1>
                <slot name="actions" />
            </div>
            <p v-if="subtitle" class="mt-3 max-w-3xl leading-relaxed text-stone-500">{{ subtitle }}</p>
        </div>
    </section>
</template>
