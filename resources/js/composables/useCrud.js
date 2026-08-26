import { onMounted, ref } from 'vue';
import axios from '../bootstrap';

/**
 * Админ CRUD-ын нийтлэг логик:
 *   GET  /api/admin/{base}        — жагсаалт ({listKey: [...]})
 *   POST /api/admin/{base}        — нэмэх/засах (id-тэй бол засна)
 *   DELETE /api/admin/{base}/{id} — устгах
 */
export function useCrud(base, listKey) {
    const items = ref([]);
    const loading = ref(true);
    const saving = ref(false);
    const errors = ref({});

    async function load(params = {}) {
        loading.value = true;
        try {
            const { data } = await axios.get(`/api/admin/${base}`, { params });
            items.value = data[listKey] ?? [];
            return data;
        } finally {
            loading.value = false;
        }
    }

    async function save(payload, { asForm = false } = {}) {
        saving.value = true;
        errors.value = {};
        try {
            let body = payload;
            if (asForm) {
                body = new FormData();
                for (const [k, v] of Object.entries(payload)) {
                    if (v === null || v === undefined) continue;
                    if (Array.isArray(v)) v.forEach((x) => body.append(`${k}[]`, x));
                    else if (typeof v === 'boolean') body.append(k, v ? '1' : '0');
                    else body.append(k, v);
                }
            }
            await axios.post(`/api/admin/${base}`, body);
            await load();
            return true;
        } catch (e) {
            errors.value = e.response?.data?.errors ?? {};
            return false;
        } finally {
            saving.value = false;
        }
    }

    async function remove(id) {
        if (!confirm('Устгахдаа итгэлтэй байна уу?')) return;
        await axios.delete(`/api/admin/${base}/${id}`);
        await load();
    }

    onMounted(load);

    return { items, loading, saving, errors, load, save, remove };
}
