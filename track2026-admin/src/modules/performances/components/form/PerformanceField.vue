<template>

    <div :class="columnClass">

        <!-- COMPONENTE PERSONALIZADO -->

        <component
            v-if="currentComponent"
            :is="currentComponent"
            :field="field"
            :form="form"
            :platforms="platforms"
        />

        <!-- INPUT -->

        <template v-else-if="isInput">

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

            <input
                :id="field.model"
                :name="field.model"
                v-model="form[field.model]"
                :type="field.type"
                :required="field.required"
                :placeholder="field.placeholder ?? ''"
                :autocomplete="field.autocomplete ?? 'off'"
                class="input"
            >

        </template>

        <!-- SELECT -->

        <template v-else-if="field.type === 'select'">

            <label
                :for="field.model"
                class="label"
            >
                {{ field.label }}
            </label>

            <select
                :id="field.model"
                :name="field.model"
                v-model="form[field.model]"
                class="input"
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

        </template>

        <!-- TEXTAREA -->

        <template v-else-if="field.type === 'textarea'">

            <label
                :for="field.model"
                class="label"
            >
                {{ field.label }}
            </label>

            <textarea
                :id="field.model"
                :name="field.model"
                v-model="form[field.model]"
                :rows="field.rows ?? 4"
                class="input resize-none"
            />

        </template>

        <!-- CHECKBOX -->

        <label
            v-else-if="field.type === 'checkbox'"
            class="checkbox"
        >

            <input
                :id="field.model"
                :name="field.model"
                v-model="form[field.model]"
                type="checkbox"
            >

            {{ field.label }}

        </label>

        <!-- FALLBACK -->

        <div
            v-else
            class="fallback"
        >

            Tipo de campo no soportado

            <strong>

                {{ field.component ?? field.type }}

            </strong>

        </div>

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

    }

})

const componentMap = {

    PerformancePlatforms,

    PerformanceSplit

}

const currentComponent = computed(() =>
    componentMap[props.field.component] ?? null
)

const isInput = computed(() => [

    'text',
    'email',
    'password',
    'number',
    'date',
    'url',
    'tel',
    'time',
    'datetime-local'

].includes(props.field.type))

const columnClass = computed(() =>
    props.field.cols === 2
        ? 'col-span-2'
        : 'col-span-1'
)

</script>

<style scoped>

.label{

    display:block;
    margin-bottom:.5rem;
    font-size:.875rem;
    font-weight:500;
    color:#d1d5db;

}

.required{

    color:#f87171;

}

.checkbox{

    display:flex;
    align-items:center;
    gap:.75rem;
    color:white;

}

.input{

    width:100%;
    padding:12px;
    border-radius:12px;
    background:#0b0f19;
    border:1px solid #1f2937;
    color:white;
    transition:.2s;

}

.input:focus{

    outline:none;
    border-color:#3b82f6;

}

.fallback{

    padding:12px;
    border-radius:12px;
    border:1px solid #991b1b;
    background:#450a0a;
    color:#fca5a5;

}

input[type="checkbox"]{

    width:18px;
    height:18px;

}

</style>