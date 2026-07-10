import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

import performanceDefaults from '../config/performanceDefaults'
import performanceFields from '../config/performanceFields'

export function usePerformanceForm() {

    const router = useRouter()

    const form = ref(performanceDefaults())
    const fields = performanceFields
    const platforms = ref([])

    const loading = ref(false)
    const saving = ref(false)
    const error = ref('')

    const reset = () => {
        form.value = performanceDefaults()
    }

    const fill = (data = {}) => {
        form.value = {
            ...performanceDefaults(),
            ...data
        }
    }

    const buildPayload = () => ({
        ...form.value,
        studio_id: form.value.studio_id || null,
        user_id: form.value.user_id || null,
        active: Number(form.value.active),
        hours_streamed: Number(form.value.hours_streamed || 0),
        ranking_score: Number(form.value.ranking_score || 0),
        platforms: form.value.platforms ?? [],
        split: {
            model_percentage: form.value.split?.model_percentage ?? 60,
            studio_percentage: form.value.split?.studio_percentage ?? 40
        }
    })

    const loadPlatforms = async () => {
        try {
            const { data } = await api.get('/platforms')
            console.log('Respuesta de la API:', data) // Log para ver qué llega
            platforms.value = data // Asignamos directamente si viene como array
            console.log('Plataformas cargadas:', platforms.value) // Log para confirmar que se asignó
        } catch (e) {
            console.error('Error al cargar plataformas:', e)
        }
    }

    const create = async () => {
        saving.value = loading.value = true
        error.value = ''
        try {
            const { data } = await api.post('/performances', buildPayload())
            router.push(`/performances/${data.data.id}`)
        } catch (e) {
            error.value = e?.response?.data?.message ?? 'Error creando modelo'
        } finally {
            saving.value = loading.value = false
        }
    }

    const update = async (id) => {
        saving.value = loading.value = true
        error.value = ''
        try {
            await api.put(`/performances/${id}`, buildPayload())
            router.push(`/performances/${id}`)
        } catch (e) {
            error.value = e?.response?.data?.message ?? 'Error actualizando modelo'
        } finally {
            saving.value = loading.value = false
        }
    }

    const load = async (id) => {
        loading.value = true
        error.value = ''
        try {
            const { data } = await api.get(`/performances/${id}`)
            fill(data.data)
        } catch (e) {
            error.value = e?.response?.data?.message ?? 'Error cargando modelo'
        } finally {
            loading.value = false
        }
    }

    onMounted(loadPlatforms)

    return {
        form,
        fields,
        platforms,
        loading,
        saving,
        error,
        reset,
        fill,
        buildPayload,
        loadPlatforms,
        create,
        update,
        load
    }
}