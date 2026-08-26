<script setup>
defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: '' },
});
const emit = defineEmits(['close']);
</script>

<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40" @click="emit('close')"></div>
            <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                <h3 v-if="title" class="mb-3 text-lg font-bold text-pine-900">{{ title }}</h3>
                <slot />
                <div class="mt-5 text-right">
                    <slot name="actions">
                        <button
                            class="rounded-full bg-pine-700 px-5 py-2 text-sm font-semibold text-white hover:bg-pine-800"
                            @click="emit('close')"
                        >Хаах</button>
                    </slot>
                </div>
            </div>
        </div>
    </Teleport>
</template>
