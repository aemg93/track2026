<template>

    <section
        class="
            overflow-hidden
            rounded-3xl
            border
            border-gray-800
            bg-gradient-to-br
            from-gray-900
            to-gray-950
        "
    >

        <header
            class="
                flex
                items-center
                justify-between
                border-b
                border-gray-800
                p-6
            "
        >

            <div>

                <h2 class="text-2xl font-bold text-white">
                    Top Ranking
                </h2>

                <p class="mt-1 text-sm text-gray-400">
                    Modelos con mejor rendimiento
                </p>

            </div>

            <span
                class="
                    rounded-xl
                    border
                    border-blue-500/20
                    bg-blue-500/10
                    px-4
                    py-2
                    text-sm
                    text-blue-400
                "
            >
                {{ ranking.length }} modelos
            </span>

        </header>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="border-b border-gray-800 bg-gray-900/80">

                    <tr>

                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-400">
                            #
                        </th>

                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-400">
                            Modelo
                        </th>

                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-400">
                            Horas
                        </th>

                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-400">
                            Score
                        </th>

                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-400">
                            Acción
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <tr
                        v-for="(item,index) in ranking"
                        :key="item.id"
                        class="
                            border-b
                            border-gray-800/50
                            transition-all
                            duration-300
                            hover:bg-gray-800/40
                        "
                    >

                        <td class="px-6 py-5">

                            <span
                                class="text-2xl"
                            >
                                {{ medal(index) }}
                            </span>

                        </td>

                        <td class="px-6 py-5">

                            <p class="font-semibold text-white">

                                {{
                                    item.first_name && item.last_name
                                        ? `${item.first_name} ${item.last_name}`
                                        : item.nickname
                                }}

                            </p>

                            <p class="text-sm text-gray-500">
                                {{ item.nickname }}
                            </p>

                        </td>

                        <td class="px-6 py-5 text-gray-300">
                            {{ item.hours_streamed ?? 0 }} h
                        </td>

                        <td class="px-6 py-5">

                            <span
                                class="
                                    rounded-xl
                                    bg-blue-500/10
                                    px-3
                                    py-1
                                    font-semibold
                                    text-blue-400
                                "
                            >
                                {{ item.ranking_score }}
                            </span>

                        </td>

                        <td class="px-6 py-5 text-right">

                            <button
                                v-if="index < 3"
                                class="
                                    rounded-xl
                                    border
                                    border-yellow-500/20
                                    bg-yellow-500/10
                                    px-4
                                    py-2
                                    text-sm
                                    font-medium
                                    text-yellow-300
                                    transition
                                    hover:bg-yellow-500/20
                                "
                            >
                                🎉 Felicitar
                            </button>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </section>

</template>

<script setup>

defineProps({

    ranking:{
        type:Array,
        default:()=>[]
    }

})

const medal = index => {

    if(index===0) return '🥇'
    if(index===1) return '🥈'
    if(index===2) return '🥉'

    return `#${index+1}`

}

</script>