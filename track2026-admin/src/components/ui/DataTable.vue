<template>

    <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">

        <!-- HEADER -->
        <div
            v-if="title || description"
            class="flex items-center justify-between p-6 border-b border-gray-800"
        >

            <div>

                <h2
                    v-if="title"
                    class="text-2xl font-bold text-white"
                >
                    {{ title }}
                </h2>

                <p
                    v-if="description"
                    class="text-gray-400 text-sm mt-1"
                >
                    {{ description }}
                </p>

            </div>

            <slot name="header" />

        </div>

        <!-- TABLE -->
        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-gray-800">

                    <tr>

                        <th
                            v-for="column in columns"
                            :key="column.key"
                            class="table-head"
                        >
                            {{ column.label }}
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <tr
                        v-for="item in items"
                        :key="item.id"
                        class="border-t border-gray-800 hover:bg-gray-800/50 transition"
                    >

                        <td
                            v-for="column in columns"
                            :key="column.key"
                            class="table-cell"
                        >

                            <slot
                                :name="column.key"
                                :item="item"
                            >

                                {{ item[column.key] }}

                            </slot>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</template>

<script setup>

defineProps({

    title: {
        type: String,
        default: '',
    },

    description: {
        type: String,
        default: '',
    },

    columns: {
        type: Array,
        required: true,
    },

    items: {
        type: Array,
        required: true,
    },

})

</script>

<style scoped>

.table-head {
    padding: 18px 24px;

    text-align: left;

    font-size: 14px;

    font-weight: 600;

    color: #9ca3af;
}

.table-cell {
    padding: 20px 24px;
}

</style>