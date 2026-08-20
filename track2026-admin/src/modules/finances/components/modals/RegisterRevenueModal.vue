<template>
    <Teleport to="body">
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm"
        >
            <div
                class="w-full max-w-xl overflow-hidden rounded-3xl border border-gray-800 bg-gradient-to-br from-gray-900 to-gray-950 shadow-2xl"
            >
                <header class="border-b border-gray-800 px-8 py-6">
                    <p class="text-xs uppercase tracking-[0.2em] text-gray-500">
                        Finanzas
                    </p>

                    <h2 class="mt-2 text-3xl font-bold text-white">
                        Registrar Ganancia
                    </h2>

                    <p class="mt-2 text-gray-400">
                        Registrar ingreso generado por plataforma.
                    </p>
                </header>

                <form class="space-y-6 p-8" @submit.prevent="save">
                    <div>
                        <label class="text-sm text-gray-300">
                            Plataforma
                        </label>

                        <select
                            v-model="form.platform_id"
                            required
                            class="mt-2 w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-white"
                        >
                            <option value="">
                                Seleccionar plataforma
                            </option>

                            <option
                                v-for="platform in platforms"
                                :key="platform.id"
                                :value="platform.id"
                            >
                                {{ platform.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm text-gray-300">
                            Fecha y hora del ingreso
                        </label>

                        <input
                            v-model="form.earned_at"
                            type="datetime-local"
                            required
                            step="1"
                            class="mt-2 w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-white"
                        >

                        <p class="mt-1 text-xs text-gray-500">
                            Indica cuándo ocurrió realmente la ganancia.
                        </p>
                    </div>

                    <div>
                        <label class="text-sm text-gray-300">
                            Tipo de ingreso
                        </label>

                        <select
                            v-model="form.original_currency"
                            required
                            class="mt-2 w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-white"
                        >
                            <option value="usd">
                                USD
                            </option>

                            <option value="tokens">
                                Tokens
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="text-sm text-gray-300">
                            {{
                                form.original_currency === 'tokens'
                                    ? 'Cantidad de tokens'
                                    : 'Cantidad USD'
                            }}
                        </label>

                        <input
                            v-model="form.original_amount"
                            type="number"
                            min="0"
                            step="0.01"
                            required
                            class="mt-2 w-full rounded-xl border border-gray-700 bg-gray-800 px-4 py-3 text-white"
                        >
                    </div>

                    <div class="flex justify-end gap-4 pt-4">
                        <button
                            type="button"
                            @click="close"
                            class="rounded-xl border border-gray-700 px-6 py-3 text-gray-300 hover:bg-gray-800"
                        >
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            :disabled="loading"
                            class="rounded-xl bg-green-600 px-6 py-3 font-semibold text-white hover:bg-green-500 disabled:opacity-50"
                        >
                            {{ loading ? 'Guardando...' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import api from '@/services/api'

const props = defineProps({
    performanceId: {
        type: [Number, String],
        required: true,
    },

    platforms: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits(['close', 'saved'])

const loading = ref(false)

/**
 * Devuelve la fecha y hora local en formato compatible
 * con <input type="datetime-local">:
 *
 * YYYY-MM-DDTHH:mm:ss
 *
 * No usamos toISOString() porque convierte la fecha a UTC.
 */
function currentDateTimeLocal() {
    const now = new Date()

    const year = now.getFullYear()
    const month = String(now.getMonth() + 1).padStart(2, '0')
    const day = String(now.getDate()).padStart(2, '0')
    const hours = String(now.getHours()).padStart(2, '0')
    const minutes = String(now.getMinutes()).padStart(2, '0')
    const seconds = String(now.getSeconds()).padStart(2, '0')

    return `${year}-${month}-${day}T${hours}:${minutes}:${seconds}`
}

const form = ref({
    platform_id: '',
    earned_at: currentDateTimeLocal(),
    original_amount: '',
    original_currency: 'usd',
})

const selectedPlatform = computed(() =>
    props.platforms.find(
        platform =>
            Number(platform.id) ===
            Number(form.value.platform_id)
    )
)

watch(selectedPlatform, (platform) => {
    if (!platform) return

    form.value.original_currency =
        platform.type === 'token'
            ? 'tokens'
            : 'usd'
})

watch(
    () => props.platforms,
    (value) => {
        console.log('========== MODAL PLATFORMS ==========')
        console.log(value)
        console.log('====================================')
    },
    {
        immediate: true,
    }
)

function close() {
    emit('close')
}

function reset() {
    form.value = {
        platform_id: '',
        earned_at: currentDateTimeLocal(),
        original_amount: '',
        original_currency: 'usd',
    }
}

async function save() {
    loading.value = true

    try {
        const payload = {
            performance_id:
                Number(props.performanceId),

            platform_id:
                Number(form.value.platform_id),

            earned_at:
                form.value.earned_at,

            original_amount:
                Number(form.value.original_amount),

            original_currency:
                form.value.original_currency,
        }

        console.log(
            '========== REGISTER EARNING PAYLOAD =========='
        )

        console.log(payload)

        console.log(
            '=============================================='
        )

        await api.post(
            '/earnings',
            payload
        )

        emit('saved')

        reset()

        close()
    } catch (error) {
        console.error(
            'ERROR REGISTERING EARNING',
            error.response?.data ?? error
        )
    } finally {
        loading.value = false
    }
}
</script>