<template>

    <div
        class="
            group
            relative
            overflow-hidden
            rounded-3xl
            p-6
            border
            transition-all
            duration-300
            hover:-translate-y-1
            hover:shadow-xl
        "
        :class="colorClass"
    >

        <!-- DECORACIÓN -->
        <div
            class="
                absolute
                -right-10
                -top-10
                w-32
                h-32
                rounded-full
                bg-white/5
                blur-2xl
                transition
                group-hover:scale-125
            "
        />


        <div class="relative z-10">


            <!-- HEADER -->
            <div
                class="
                    flex
                    items-center
                    justify-between
                    mb-4
                "
            >

                <p
                    class="
                        text-gray-400
                        text-sm
                        font-medium
                    "
                >
                    {{ title }}
                </p>


                <span
                    v-if="icon"
                    class="
                        text-xl
                        opacity-80
                    "
                >
                    {{ icon }}
                </span>


            </div>



            <!-- VALUE -->

            <h2
                class="
                    text-4xl
                    font-bold
                    text-white
                    tracking-tight
                "
            >

                {{ value }}

            </h2>



            <!-- FOOTER -->

            <div
                class="
                    mt-5
                    flex
                    items-center
                    justify-between
                    gap-3
                "
            >

                <p
                    class="
                        text-sm
                    "
                    :class="textColorClass"
                >

                    {{ description }}

                </p>



                <span
                    v-if="trend"
                    class="
                        text-xs
                        font-semibold
                        px-3
                        py-1
                        rounded-full
                        bg-white/10
                        text-white
                    "
                >

                    {{ trend }}

                </span>


            </div>


        </div>


    </div>


</template>


<script setup>


defineProps({

    title: {

        type: String,

        required: true

    },


    value: {

        type: [String, Number],

        required: true

    },


    description: {

        type: String,

        default: ''

    },


    colorClass: {

        type: String,

        default: ''

    },


    textColorClass: {

        type: String,

        default: ''

    },


    icon: {

        type: String,

        default: null

    },


    trend: {

        type: String,

        default: null

    }


})


</script>