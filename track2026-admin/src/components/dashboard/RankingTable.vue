<template>

    <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">

        <div class="p-6 border-b border-gray-800">

            <h2 class="text-2xl font-bold text-white">
                Rendimiento
            </h2>

            <p class="text-gray-400 text-sm mt-1">
                Modelos destacadas
            </p>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-gray-800">

                    <tr>

                        <th class="table-head">
                            Modelo
                        </th>

                        <th class="table-head">
                            Horas
                        </th>

                        <th class="table-head">
                            Score
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <tr
                        v-for="item in ranking"
                        :key="item.id"
                        class="border-t border-gray-800 hover:bg-gray-800/40 transition"
                    >

                        <td class="table-cell">

                            <div>

                                <p class="text-white font-semibold">
                                    {{ item.name }}
                                </p>

                                <p class="text-sm text-gray-500">
                                    Modelo #{{ item.id }}
                                </p>

                            </div>

                        </td>

                        <td class="table-cell text-white">
                            {{ item.hours_streamed }} hrs
                        </td>

                        <td class="table-cell">

                            <span class="bg-blue-500/20 text-blue-400 px-3 py-1 rounded-xl text-sm font-semibold">
                                {{ item.ranking_score }}
                            </span>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</template>

<script setup>

defineProps({
    ranking: {
        type: Array,
        default: () => [],
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