<template>
    <div :class="columnClass">

        <!-- Componentes personalizados -->
        <component
            v-if="currentComponent"
            :is="currentComponent"
            :field="field"
            :form="form"
            :platforms="platforms"
            :errors="errors"
        />

        <!-- Campo estándar -->
        <template v-else>

            <template v-if="field.type !== 'checkbox'">

                <label
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

            </template>

            <!-- Input -->
            <input
                v-if="isInput"
                :id="field.model"
                v-model="form[field.model]"
                :type="field.type"
                :name="field.model"
                :required="field.required"
                :placeholder="field.placeholder ?? ''"
                :autocomplete="field.autocomplete ?? 'off'"
                :aria-invalid="hasError"
                class="input"
                :class="{ error: hasError }"
            >

            <!-- Select -->
            <select
                v-else-if="field.type === 'select'"
                :id="field.model"
                v-model="form[field.model]"
                :name="field.model"
                :required="field.required"
                :aria-invalid="hasError"
                class="input"
                :class="{ error: hasError }"
            >
                <option value="">
                    Seleccione...
                </option>

                <option
                    v-for="option in field.options"
                    :key="option.value"
                    :value="option.value"
                >
                    {{ option.label }}
                </option>
            </select>

            <!-- Textarea -->
            <textarea
                v-else-if="field.type === 'textarea'"
                :id="field.model"
                v-model="form[field.model]"
                :name="field.model"
                :rows="field.rows ?? 4"
                :required="field.required"
                :aria-invalid="hasError"
                class="input resize-none"
                :class="{ error: hasError }"
            />

            <!-- Checkbox -->
            <label
                v-else-if="field.type === 'checkbox'"
                class="checkbox"
            >

                <input
                    :id="field.model"
                    v-model="form[field.model]"
                    :name="field.model"
                    type="checkbox"
                >

                {{ field.label }}

            </label>

            <!-- Campo no soportado -->
            <div
                v-else
                class="fallback"
            >
                <strong>Campo no soportado:</strong>

                {{ field.component ?? field.type }}

            </div>

            <p
                v-if="fieldError"
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
        required: true
    },

    form: {
        type: Object,
        required: true
    },

    platforms: {
        type: Array,
        default: () => []
    },

    errors: {
        type: Object,
        default: () => ({})
    }

})

const componentMap = Object.freeze({

    PerformancePlatforms,
    PerformanceSplit

})

const inputTypes = Object.freeze(new Set([

    'text',
    'email',
    'password',
    'number',
    'date',
    'url',
    'tel',
    'time',
    'datetime-local'

]))

const currentComponent = computed(() =>
    componentMap[props.field.component] ?? null
)

const isInput = computed(() =>
    inputTypes.has(props.field.type)
)

const fieldError = computed(() =>
    props.errors[props.field.model]?.[0] ?? ''
)

const hasError = computed(() =>
    fieldError.value.length > 0
)

const columnClass = computed(() =>
    props.field.cols === 2
        ? 'col-span-2'
        : 'col-span-1'
)

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
    color: #ef4444;
}

.checkbox {
    display: flex;
    align-items: center;
    gap: .75rem;
    color: #fff;
}

.input {
    width: 100%;
    padding: 12px;
    border: 1px solid #1f2937;
    border-radius: 12px;
    background: #0b0f19;
    color: #fff;
    transition: .2s ease;
}

.input:focus {
    outline: none;
    border-color: #3b82f6;
}

.input.error {
    border-color: #ef4444;
    box-shadow: 0 0 0 2px rgb(239 68 68 / .15);
}

.error-message {
    margin-top: .5rem;
    font-size: .875rem;
    color: #ef4444;
}

.fallback {
    padding: 12px;
    border: 1px solid #991b1b;
    border-radius: 12px;
    background: #450a0a;
    color: #fca5a5;
}

input[type="checkbox"] {
    width: 18px;
    height: 18px;
}

</style>