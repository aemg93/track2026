<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const auth = useAuthStore()

const email = ref('admin@example.com')
const password = ref('123456')

const loading = ref(false)
const error = ref(null)

const submit = async () => {
    try {
        loading.value = true
        error.value = null

        await auth.login(email.value, password.value)

        router.push('/dashboard')
    } catch (err) {
        error.value = 'Credenciales inválidas'
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div
        class="
            relative
            min-h-screen
            flex
            overflow-hidden
            bg-slate-950
        "
    >

        <!-- Fondo decorativo -->
        <div class="absolute inset-0 pointer-events-none">

            <!-- Patrón diagonal -->
            <div
                class="
                    absolute
                    inset-0
                    opacity-[0.04]
                    bg-[linear-gradient(45deg,white_1px,transparent_1px)]
                    bg-[length:45px_45px]
                "
            ></div>


            <!-- Círculo superior -->
            <div
                class="
                    absolute
                    -top-48
                    right-20
                    h-[500px]
                    w-[500px]
                    rounded-full
                    bg-cyan-400/10
                    blur-3xl
                "
            ></div>


            <!-- Círculo inferior -->
            <div
                class="
                    absolute
                    -bottom-64
                    left-1/3
                    h-[600px]
                    w-[600px]
                    rounded-full
                    border
                    border-cyan-400/10
                "
            ></div>


            <!-- Figura geométrica -->
            <div
                class="
                    absolute
                    top-32
                    right-[20%]
                    h-48
                    w-48
                    rotate-45
                    border
                    border-white/10
                    rounded-3xl
                "
            ></div>


            <!-- Líneas flotantes -->
            <div
                class="
                    absolute
                    bottom-20
                    right-10
                    h-32
                    w-80
                    rotate-12
                    border-t
                    border-cyan-400/10
                "
            ></div>


        </div>



        <!-- Branding -->
        <section
            class="
                relative
                z-10
                hidden
                lg:flex
                flex-1
                flex-col
                justify-center
                px-20
                text-white
            "
        >

            <h1
                class="
                    text-6xl
                    font-bold
                    tracking-tight
                "
            >
                TRACK<span class="text-cyan-400">2026</span>
            </h1>


            <p
                class="
                    mt-6
                    max-w-lg
                    text-xl
                    text-slate-300
                "
            >
                Plataforma integral para la gestión operativa,
                financiera y administrativa de estudios webcam.
            </p>


            <div
                class="
                    mt-10
                    space-y-4
                    text-slate-300
                "
            >

                <div class="flex items-center gap-3">
                    <span class="text-cyan-400 text-xl">
                        ✓
                    </span>
                    Gestión de modelos
                </div>


                <div class="flex items-center gap-3">
                    <span class="text-cyan-400 text-xl">
                        ✓
                    </span>
                    Control de turnos
                </div>


                <div class="flex items-center gap-3">
                    <span class="text-cyan-400 text-xl">
                        ✓
                    </span>
                    Reportes financieros
                </div>

            </div>


        </section>




        <!-- Login -->
        <section
            class="
                relative
                z-10
                flex
                flex-1
                items-center
                justify-center
                px-6
            "
        >

            <div
                class="
                    w-full
                    max-w-md
                    rounded-3xl
                    bg-white/95
                    backdrop-blur-xl
                    p-10
                    shadow-2xl
                    ring-1
                    ring-white/20
                "
            >

                <div class="mb-8">

                    <h2
                        class="
                            text-3xl
                            font-bold
                            text-slate-900
                        "
                    >
                        Bienvenido
                    </h2>


                    <p
                        class="
                            mt-2
                            text-slate-500
                        "
                    >
                        Ingresa a tu cuenta para continuar
                    </p>

                </div>




                <form
                    @submit.prevent="submit"
                    class="space-y-5"
                >

                    <div>

                        <label
                            class="
                                text-sm
                                text-slate-600
                            "
                        >
                            Correo electrónico
                        </label>


                        <input
                            v-model="email"
                            type="email"
                            class="
                                mt-2
                                w-full
                                rounded-xl
                                border
                                border-slate-200
                                px-4
                                py-3
                                outline-none
                                transition
                                focus:ring-2
                                focus:ring-cyan-400
                            "
                            placeholder="correo@ejemplo.com"
                        />

                    </div>




                    <div>

                        <label
                            class="
                                text-sm
                                text-slate-600
                            "
                        >
                            Contraseña
                        </label>


                        <input
                            v-model="password"
                            type="password"
                            class="
                                mt-2
                                w-full
                                rounded-xl
                                border
                                border-slate-200
                                px-4
                                py-3
                                outline-none
                                transition
                                focus:ring-2
                                focus:ring-cyan-400
                            "
                            placeholder="••••••••"
                        />

                    </div>




                    <button
                        :disabled="loading"
                        class="
                            w-full
                            rounded-xl
                            bg-cyan-500
                            py-3
                            font-semibold
                            text-white
                            transition
                            hover:bg-cyan-600
                            disabled:opacity-50
                        "
                    >
                        {{
                            loading
                                ? 'Ingresando...'
                                : 'Iniciar sesión'
                        }}
                    </button>




                    <p
                        v-if="error"
                        class="
                            rounded-lg
                            bg-red-50
                            p-3
                            text-center
                            text-sm
                            text-red-600
                        "
                    >
                        {{ error }}
                    </p>


                </form>

            </div>

        </section>


    </div>
</template>