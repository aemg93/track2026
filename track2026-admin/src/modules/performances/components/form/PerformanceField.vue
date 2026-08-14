<template>
    <div :class="columnClass">

        <!-- =========================================================
             COMPONENTES PERSONALIZADOS
             ========================================================= -->
        <component
            v-if="currentComponent"
            :is="currentComponent"
            :field="field"
            :form="form"
            :platforms="platforms"
            :errors="errors"
        />

        <!-- =========================================================
             CAMPOS ESTÁNDAR
             ========================================================= -->
        <template v-else>

            <!-- Label -->
            <label
                v-if="field.type !== 'checkbox'"
                :for="field.model"
                class="label"
            >
                {{ field.label }}

                <span
                    v-if="field.required"
                    class="required"
                >
                    *
                </span>
            </label>

            <!-- =====================================================
                 INPUT
                 ===================================================== -->
            <input
                v-if="isInput"
                :id="field.model"
                v-model="form[field.model]"
                :type="field.type"
                :name="field.model"
                :required="field.required"
                :placeholder="field.placeholder ?? ''"
                :autocomplete="field.autocomplete ?? 'off'"
                :min="field.min"
                :max="field.max"
                :step="field.step"
                :aria-invalid="hasError"
                :aria-describedby="hasError ? `${field.model}-error` : undefined"
                class="input"
                :class="{ error: hasError }"
            >

            <!-- =====================================================
                 SELECT
                 ===================================================== -->
            <select
                v-else-if="field.type === 'select'"
                :id="field.model"
                v-model="form[field.model]"
                :name="field.model"
                :required="field.required"
                :aria-invalid="hasError"
                :aria-describedby="hasError ? `${field.model}-error` : undefined"
                class="input select-input"
                :class="{ error: hasError }"
            >
                <option
                    value=""
                    disabled
                >
                    {{ field.placeholder ?? 'Seleccione...' }}
                </option>

                <option
                    v-for="option in normalizedOptions"
                    :key="String(option.value)"
                    :value="option.value"
                >
                    {{ option.label }}
                </option>
            </select>

            <!-- =====================================================
                 TEXTAREA
                 ===================================================== -->
            <textarea
                v-else-if="field.type === 'textarea'"
                :id="field.model"
                v-model="form[field.model]"
                :name="field.model"
                :rows="field.rows ?? 4"
                :maxlength="field.maxlength"
                :placeholder="field.placeholder ?? ''"
                :required="field.required"
                :aria-invalid="hasError"
                :aria-describedby="hasError ? `${field.model}-error` : undefined"
                class="input resize-none"
                :class="{ error: hasError }"
            />

            <!-- =====================================================
                 CHECKBOX
                 ===================================================== -->
            <label
                v-else-if="field.type === 'checkbox'"
                class="checkbox"
                :class="{ 'checkbox-error': hasError }"
            >
                <input
                    :id="field.model"
                    v-model="form[field.model]"
                    :name="field.model"
                    type="checkbox"
                    :aria-invalid="hasError"
                    :aria-describedby="hasError ? `${field.model}-error` : undefined"
                >

                <span>
                    {{ field.label }}

                    <span
                        v-if="field.required"
                        class="required"
                    >
                        *
                    </span>
                </span>
            </label>

            <!-- =====================================================
                 CAMPO NO SOPORTADO
                 ===================================================== -->
            <div
                v-else
                class="fallback"
            >
                <strong>Campo no soportado:</strong>

                {{ field.component ?? field.type }}
            </div>

            <!-- =====================================================
                 ERROR DE VALIDACIÓN
                 ===================================================== -->
            <p
                v-if="fieldError"
                :id="`${field.model}-error`"
                class="error-message"
            >
                {{ fieldError }}
            </p>

        </template>

    </div>
</template>

<script setup>

import { computed } from 'vue'

import PerformancePlatforms from './PerformancePlatforms.vue'
import PerformanceSplit from './PerformanceSplit.vue'

const props = defineProps({

    field: {
        type: Object,
        required: true,
    },

    form: {
        type: Object,
        required: true,
    },

    platforms: {
        type: Array,
        default: () => [],
    },

    errors: {
        type: Object,
        default: () => ({}),
    },

})

/*
|--------------------------------------------------------------------------
| Componentes personalizados
|--------------------------------------------------------------------------
*/

const componentMap = Object.freeze({
    PerformancePlatforms,
    PerformanceSplit,
})

const currentComponent = computed(() => {
    if (!props.field.component) {
        return null
    }

    return componentMap[props.field.component] ?? null
})

/*
|--------------------------------------------------------------------------
| Tipos de input soportados
|--------------------------------------------------------------------------
*/

const inputTypes = Object.freeze(
    new Set([
        'text',
        'email',
        'password',
        'number',
        'date',
        'url',
        'tel',
        'time',
        'datetime-local',
        'month',
        'week',
        'search',
    ]),
)

const isInput = computed(() =>
    inputTypes.has(props.field.type),
)

/*
|--------------------------------------------------------------------------
| Opciones del select
|--------------------------------------------------------------------------
|
| Permite trabajar con:
|
| [
|     {
|         value: 'night',
|         label: 'Noche',
|     }
| ]
|
| y también evita errores si options no existe.
|
*/

const normalizedOptions = computed(() => {
    if (!Array.isArray(props.field.options)) {
        return []
    }

    return props.field.options
        .filter(option =>
            option !== null &&
            option !== undefined,
        )
        .map(option => {

            if (
                typeof option === 'object' &&
                Object.prototype.hasOwnProperty.call(
                    option,
                    'value',
                )
            ) {
                return {
                    value: option.value,
                    label:
                        option.label ??
                        String(option.value),
                }
            }

            return {
                value: option,
                label: String(option),
            }
        })
})

/*
|--------------------------------------------------------------------------
| Errores
|--------------------------------------------------------------------------
|
| Laravel normalmente devuelve:
|
| {
|     work_shift: [
|         "Debes seleccionar un turno."
|     ]
| }
|
| Pero también podemos recibir:
|
| {
|     work_shift: "Debes seleccionar un turno."
| }
|
*/

const fieldError = computed(() => {

    const value =
        props.errors?.[props.field.model]

    if (Array.isArray(value)) {
        return value[0] ?? ''
    }

    if (
        typeof value === 'string' &&
        value.length > 0
    ) {
        return value
    }

    return ''
})

const hasError = computed(() =>
    Boolean(fieldError.value),
)

/*
|--------------------------------------------------------------------------
| Columnas
|--------------------------------------------------------------------------
*/

const columnClass = computed(() => {

    if (props.field.cols === 2) {
        return 'col-span-1 md:col-span-2'
    }

    if (props.field.cols === 3) {
        return 'col-span-1 md:col-span-3'
    }

    return 'col-span-1'
})

</script>

<style scoped>

.label {
    display: block;
    margin-bottom: .5rem;
    font-size: .875rem;
    font-weight: 500;
    color: #d1d5db;
}

.required {
    margin-left: .15rem;
    color: #ef4444;
}

.input {
    width: 100%;
    min-height: 44px;
    padding: 11px 12px;
    border: 1px solid #1f2937;
    border-radius: 12px;
    background: #0b0f19;
    color: #fff;
    transition:
        border-color .2s ease,
        box-shadow .2s ease,
        background-color .2s ease;
}

.input::placeholder {
    color: #6b7280;
}

.input:hover {
    border-color: #374151;
}

.input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgb(59 130 246 / .15);
}

.input.error {
    border-color: #ef4444;
    box-shadow: 0 0 0 2px rgb(239 68 68 / .15);
}

.select-input {
    cursor: pointer;
}

.select-input option {
    background: #0b0f19;
    color: #fff;
}

textarea.input {
    min-height: auto;
}

.checkbox {
    display: flex;
    align-items: center;
    gap: .75rem;
    min-height: 44px;
    color: #fff;
    cursor: pointer;
    user-select: none;
}

.checkbox input {
    width: 18px;
    height: 18px;
    margin: 0;
    cursor: pointer;
    accent-color: #3b82f6;
}

.checkbox-error {
    color: #fca5a5;
}

.checkbox-error input {
    outline: 1px solid #ef4444;
    outline-offset: 2px;
}

.error-message {
    margin-top: .5rem;
    font-size: .875rem;
    line-height: 1.4;
    color: #f87171;
}

.fallback {
    padding: 12px;
    border: 1px solid #991b1b;
    border-radius: 12px;
    background: #450a0a;
    color: #fca5a5;
}

</style>