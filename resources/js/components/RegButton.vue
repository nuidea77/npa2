<script setup>
import { ref } from 'vue';
import { t } from '../i18n';
import Modal from './Modal.vue';

/**
 * Бүртгүүлэх товч — бүртгэлийн төлөвөөс хамаарч өөр өөр үйлдэлтэй:
 *  - open       → бүртгэлийн маягт руу орно
 *  - not_started→ «Бүртгэл эхлээгүй байна» анхааруулга
 *  - closed     → «Бүртгэл дууссан байна» анхааруулга
 *  - soon       → идэвхгүй «Тун удахгүй...»
 */
const props = defineProps({
    event: { type: Object, required: true },
    label: { type: String, default: '' },
});

const warning = ref('');

function onClick() {
    const s = props.event.reg_state;
    if (s === 'not_started') warning.value = t('reg.notStarted');
    else if (s === 'closed') warning.value = t('reg.closed');
}
</script>

<template>
    <div>
        <router-link
            v-if="event.reg_state === 'open'"
            :to="`/events/${event.id}/register`"
            class="inline-block rounded-full bg-sand-400 px-6 py-2.5 text-sm font-bold text-pine-950 shadow transition hover:bg-sand-300"
        >{{ label || t('reg.open') }}</router-link>

        <button
            v-else-if="event.reg_state === 'soon'"
            class="inline-block cursor-not-allowed rounded-full bg-stone-200 px-6 py-2.5 text-sm font-bold text-stone-500"
            disabled
        >{{ t('reg.soon') }}</button>

        <button
            v-else
            class="inline-block rounded-full px-6 py-2.5 text-sm font-bold shadow transition"
            :class="event.reg_state === 'closed'
                ? 'bg-stone-300 text-stone-600 hover:bg-stone-200'
                : 'bg-sand-200 text-sand-900 hover:bg-sand-100'"
            @click="onClick"
        >{{ label || t('reg.open') }}</button>

        <div v-if="event.reg_start && event.reg_end" class="mt-2 text-xs text-stone-500">
            {{ t('common.date') }}: {{ event.reg_start }} — {{ event.reg_end }}
        </div>

        <Modal :show="!!warning" @close="warning = ''">
            <p class="text-stone-700">{{ warning }}</p>
        </Modal>
    </div>
</template>
