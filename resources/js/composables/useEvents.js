import { onMounted, ref } from 'vue';
import axios from '../bootstrap';

/** Тухайн хөтөлбөрийн арга хэмжээнүүдийг (жилээр эрэмбэлсэн) татна. */
export function useEvents(program) {
    const events = ref([]);
    const loading = ref(true);

    onMounted(async () => {
        try {
            const { data } = await axios.get('/api/events');
            events.value = data.events.filter((e) => e.program === program);
        } finally {
            loading.value = false;
        }
    });

    return { events, loading };
}
