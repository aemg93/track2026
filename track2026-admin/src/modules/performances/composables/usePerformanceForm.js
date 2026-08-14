import { onMounted, ref } from 'vue'

import api from '@/services/api'

import performanceDefaults from '@/modules/performances/config/performanceDefaults'
import performanceFields from '@/modules/performances/config/performanceFields'

export function usePerformanceForm() {
  /*
  |--------------------------------------------------------------------------
  | Estado
  |--------------------------------------------------------------------------
  */

  const form = ref(
    performanceDefaults(),
  )

  const fields = performanceFields

  const platforms = ref([])

  const loading = ref(false)

  const saving = ref(false)

  const error = ref('')

  const errors = ref({})

  /*
  |--------------------------------------------------------------------------
  | Errores
  |--------------------------------------------------------------------------
  */

  const clearErrors = () => {
    error.value = ''
    errors.value = {}
  }

  const getFieldError = field => {
    const fieldErrors =
      errors.value?.[field]

    if (Array.isArray(fieldErrors)) {
      return fieldErrors[0] ?? ''
    }

    if (typeof fieldErrors === 'string') {
      return fieldErrors
    }

    return ''
  }

  /*
  |--------------------------------------------------------------------------
  | Reset
  |--------------------------------------------------------------------------
  */

  const reset = () => {
    form.value = performanceDefaults()

    clearErrors()
  }

  /*
  |--------------------------------------------------------------------------
  | Normalizadores
  |--------------------------------------------------------------------------
  */

  const normalizePlatforms = (
    items = [],
  ) => {
    if (!Array.isArray(items)) {
      return []
    }

    return items
      .map(item => {
        if (
          item &&
          typeof item === 'object'
        ) {
          return Number(item.id)
        }

        return Number(item)
      })
      .filter(
        id =>
          Number.isInteger(id) &&
          id > 0,
      )
  }

  const normalizeNumber = (
    value,
    fallback = 0,
  ) => {
    if (
      value === null ||
      value === undefined ||
      value === ''
    ) {
      return fallback
    }

    const number = Number(value)

    return Number.isFinite(number)
      ? number
      : fallback
  }

  const normalizeBoolean = value => {
    if (typeof value === 'boolean') {
      return value
    }

    if (
      value === 1 ||
      value === '1' ||
      value === 'true'
    ) {
      return true
    }

    return false
  }

  const normalizeWorkShift = value => {
    const validShifts = [
      'morning',
      'afternoon',
      'night',
    ]

    return validShifts.includes(value)
      ? value
      : null
  }

  /*
  |--------------------------------------------------------------------------
  | Cargar datos en el formulario
  |--------------------------------------------------------------------------
  */

  const fill = (data = {}) => {
    const defaults =
      performanceDefaults()

    form.value = {
      ...defaults,
      ...data,

      studio_id:
        data.studio_id ??
        defaults.studio_id ??
        null,

      user_id:
        data.user_id ??
        defaults.user_id ??
        null,

      work_shift:
        normalizeWorkShift(
          data.work_shift ??
          defaults.work_shift ??
          null,
        ),

      active:
        normalizeBoolean(
          data.active ??
          defaults.active,
        ),

      hours_streamed:
        normalizeNumber(
          data.hours_streamed,
          0,
        ),

      ranking_score:
        normalizeNumber(
          data.ranking_score,
          0,
        ),

      platforms:
        normalizePlatforms(
          data.platforms,
        ),

      split: {
        ...(defaults.split ?? {}),

        model_percentage:
          normalizeNumber(
            data.split
              ?.model_percentage,
            60,
          ),

        studio_percentage:
          normalizeNumber(
            data.split
              ?.studio_percentage,
            40,
          ),
      },
    }
  }

  /*
  |--------------------------------------------------------------------------
  | Construcción del payload
  |--------------------------------------------------------------------------
  */

  const buildPayload = () => {
    const modelPercentage =
      normalizeNumber(
        form.value.split
          ?.model_percentage,
        60,
      )

    const studioPercentage =
      normalizeNumber(
        form.value.split
          ?.studio_percentage,
        40,
      )

    const workShift =
      normalizeWorkShift(
        form.value.work_shift,
      )

    return {
      /*
       * Datos personales
       */
      first_name:
        form.value.first_name ?? '',

      last_name:
        form.value.last_name ?? '',

      nickname:
        form.value.nickname || null,

      birth_date:
        form.value.birth_date || null,

      /*
       * Contacto
       */
      email:
        form.value.email ?? '',

      phone:
        form.value.phone || null,

      /*
       * Ubicación
       */
      country:
        form.value.country || null,

      city:
        form.value.city || null,

      address:
        form.value.address || null,

      /*
       * Documento
       */
      document_type:
        form.value.document_type || null,

      document_number:
        form.value.document_number || null,

      /*
       * Sistema
       */
      studio_id:
        form.value.studio_id || null,

      user_id:
        form.value.user_id || null,

      work_shift:
        workShift,

      active:
        normalizeBoolean(
          form.value.active,
        ),

      /*
       * Estadísticas iniciales
       */
      hours_streamed:
        normalizeNumber(
          form.value.hours_streamed,
          0,
        ),

      ranking_score:
        normalizeNumber(
          form.value.ranking_score,
          0,
        ),

      /*
       * Plataformas
       */
      platforms:
        normalizePlatforms(
          form.value.platforms,
        ),

      /*
       * Distribución financiera
       */
      split: {
        model_percentage:
          modelPercentage,

        studio_percentage:
          studioPercentage,
      },
    }
  }

  /*
  |--------------------------------------------------------------------------
  | Manejo de errores API
  |--------------------------------------------------------------------------
  */

  const handleError = (
    exception,
    fallbackMessage,
  ) => {
    clearErrors()

    const response =
      exception?.response

    if (response?.status === 422) {
      errors.value =
        response.data?.errors ?? {}

      error.value =
        response.data?.message ??
        'Los datos enviados no son válidos.'

      return
    }

    error.value =
      response?.data?.message ??
      fallbackMessage
  }

  /*
  |--------------------------------------------------------------------------
  | Plataformas
  |--------------------------------------------------------------------------
  */

  const loadPlatforms = async () => {
    try {
      const response =
        await api.get('/platforms')

      const data =
        response?.data

      if (Array.isArray(data)) {
        platforms.value = data

        return
      }

      if (
        Array.isArray(
          data?.data,
        )
      ) {
        platforms.value =
          data.data

        return
      }

      platforms.value = []
    } catch (exception) {
      handleError(
        exception,
        'Error cargando plataformas',
      )
    }
  }

  /*
  |--------------------------------------------------------------------------
  | Crear modelo
  |--------------------------------------------------------------------------
  */

  const create = async () => {
    loading.value = true

    saving.value = true

    clearErrors()

    try {
      const payload =
        buildPayload()

      const response =
        await api.post(
          '/performances',
          payload,
        )

      return (
        response?.data?.data ??
        response?.data ??
        null
      )
    } catch (exception) {
      handleError(
        exception,
        'Error creando modelo',
      )

      return null
    } finally {
      loading.value = false

      saving.value = false
    }
  }

  /*
  |--------------------------------------------------------------------------
  | Actualizar modelo
  |--------------------------------------------------------------------------
  */

  const update = async id => {
    if (!id) {
      error.value =
        'No se recibió el ID del modelo.'

      return null
    }

    loading.value = true

    saving.value = true

    clearErrors()

    try {
      const payload =
        buildPayload()

      const response =
        await api.put(
          `/performances/${id}`,
          payload,
        )

      return (
        response?.data?.data ??
        response?.data ??
        null
      )
    } catch (exception) {
      handleError(
        exception,
        'Error actualizando modelo',
      )

      return null
    } finally {
      loading.value = false

      saving.value = false
    }
  }

  /*
  |--------------------------------------------------------------------------
  | Cargar modelo
  |--------------------------------------------------------------------------
  */

  const load = async id => {
    if (!id) {
      return null
    }

    loading.value = true

    clearErrors()

    try {
      const response =
        await api.get(
          `/performances/${id}`,
        )

      const data =
        response?.data?.data ??
        response?.data ??
        {}

      fill(data)

      return data
    } catch (exception) {
      handleError(
        exception,
        'Error cargando modelo',
      )

      return null
    } finally {
      loading.value = false
    }
  }

  /*
  |--------------------------------------------------------------------------
  | Inicialización
  |--------------------------------------------------------------------------
  */

  onMounted(() => {
    loadPlatforms()
  })

  /*
  |--------------------------------------------------------------------------
  | API pública del composable
  |--------------------------------------------------------------------------
  */

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

    load,
  }
}
