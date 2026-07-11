import { onMounted, ref } from 'vue'
import api from '@/services/api'
import performanceDefaults from '../config/performanceDefaults'
import performanceFields from '../config/performanceFields'

export function usePerformanceForm() {
  const form = ref(performanceDefaults())
  const fields = performanceFields
  const platforms = ref([])
  const loading = ref(false)
  const saving = ref(false)
  const error = ref('')
  const errors = ref({})

  const reset = () => {
    form.value = performanceDefaults()
    clearErrors()
  }

  const clearErrors = () => {
    error.value = ''
    errors.value = {}
  }

  const getFieldError = field => errors.value[field]?.[0] ?? ''

  const normalizePlatforms = (items = []) => {
    if (!Array.isArray(items)) return []
    return items.map(item =>
      Number(typeof item === 'object' ? item.id : item)
    )
  }

  const fill = (data = {}) => {
    form.value = {
      ...performanceDefaults(),
      ...data,
      studio_id: data.studio_id ?? null,
      user_id: data.user_id ?? null,
      active: Boolean(data.active),
      hours_streamed: Number(data.hours_streamed ?? 0),
      ranking_score: Number(data.ranking_score ?? 0),
      platforms: normalizePlatforms(data.platforms),
      split: {
        model_percentage: Number(data.split?.model_percentage ?? 60),
        studio_percentage: Number(data.split?.studio_percentage ?? 40)
      }
    }
  }

  const buildPayload = () => ({
    ...form.value,
    studio_id: form.value.studio_id || null,
    user_id: form.value.user_id || null,
    active: Boolean(form.value.active),
    hours_streamed: Number(form.value.hours_streamed || 0),
    ranking_score: Number(form.value.ranking_score || 0),
    platforms: normalizePlatforms(form.value.platforms),
    split: {
      model_percentage: Number(form.value.split?.model_percentage ?? 60),
      studio_percentage: Number(form.value.split?.studio_percentage ?? 40)
    }
  })

  const handleError = (e, fallback) => {
    clearErrors()
    if (e?.response?.status === 422) {
      errors.value = e.response.data.errors ?? {}
      return
    }
    error.value = e?.response?.data?.message ?? fallback
  }

  const loadPlatforms = async () => {
    try {
      const { data } = await api.get('/platforms')
      platforms.value = Array.isArray(data) ? data : data.data ?? []
    } catch (e) {
      handleError(e, 'Error cargando plataformas')
    }
  }

  const create = async () => {
    loading.value = true
    saving.value = true
    clearErrors()
    try {
      const { data } = await api.post('/performances', buildPayload())
      return data.data
    } catch (e) {
      handleError(e, 'Error creando modelo')
      return null
    } finally {
      loading.value = false
      saving.value = false
    }
  }

  const update = async id => {
    loading.value = true
    saving.value = true
    clearErrors()
    try {
      const { data } = await api.put(`/performances/${id}`, buildPayload())
      return data.data
    } catch (e) {
      handleError(e, 'Error actualizando modelo')
      return null
    } finally {
      loading.value = false
      saving.value = false
    }
  }

  const load = async id => {
    loading.value = true
    clearErrors()
    try {
      const { data } = await api.get(`/performances/${id}`)
      fill(data.data)
    } catch (e) {
      handleError(e, 'Error cargando modelo')
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
    errors,
    getFieldError,
    reset,
    fill,
    buildPayload,
    loadPlatforms,
    create,
    update,
    load
  }
}
