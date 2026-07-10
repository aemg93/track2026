export default [

    {
        id: 'personal',

        title: 'Datos personales',

        columns: 2,

        fields: [

            {
                model: 'first_name',
                label: 'Nombre',
                type: 'text',
                required: true
            },

            {
                model: 'last_name',
                label: 'Apellido',
                type: 'text',
                required: true
            },

            {
                model: 'nickname',
                label: 'Apodo',
                type: 'text'
            },

            {
                model: 'birth_date',
                label: 'Fecha de nacimiento',
                type: 'date',
                required: true
            }

        ]

    },

    {
        id: 'contact',

        title: 'Contacto',

        columns: 2,

        fields: [

            {
                model: 'email',
                label: 'Correo electrónico',
                type: 'email',
                required: true
            },

            {
                model: 'phone',
                label: 'Teléfono',
                type: 'tel'
            }

        ]

    },

    {
        id: 'location',

        title: 'Ubicación',

        columns: 2,

        fields: [

            {
                model: 'country',
                label: 'País',
                type: 'text'
            },

            {
                model: 'city',
                label: 'Ciudad',
                type: 'text'
            },

            {
                model: 'address',
                label: 'Dirección',
                type: 'textarea',
                cols: 2,
                rows: 3
            }

        ]

    },

    {
        id: 'document',

        title: 'Documento',

        columns: 2,

        fields: [

            {
                model: 'document_type',
                label: 'Tipo de documento',
                type: 'select',

                options: [

                    {
                        value: 'cc',
                        label: 'Cédula de Ciudadanía'
                    },

                    {
                        value: 'ce',
                        label: 'Cédula de Extranjería'
                    },

                    {
                        value: 'passport',
                        label: 'Pasaporte'
                    }

                ]

            },

            {
                model: 'document_number',
                label: 'Número de documento',
                type: 'text'
            }

        ]

    },

    {
        id: 'platforms',

        title: 'Plataformas',

        columns: 1,

        fields: [

            {
                model: 'platforms',
                label: 'Plataformas',
                component: 'PerformancePlatforms'
            }

        ]

    },

    {
        id: 'split',

        title: 'Distribución de ganancias',

        columns: 1,

        fields: [

            {
                model: 'split',
                component: 'PerformanceSplit'
            }

        ]

    },

    {
        id: 'system',

        title: 'Sistema',

        columns: 2,

        fields: [

            {
                model: 'studio_id',
                label: 'Studio',
                type: 'number'
            },

            {
                model: 'user_id',
                label: 'Usuario',
                type: 'number'
            },

            {
                model: 'active',
                label: 'Modelo activa',
                type: 'checkbox',
                cols: 2
            }

        ]

    },

    {
        id: 'statistics',

        title: 'Estadísticas iniciales',

        columns: 2,

        fields: [

            {
                model: 'hours_streamed',
                label: 'Horas transmitidas',
                type: 'number'
            },

            {
                model: 'ranking_score',
                label: 'Ranking',
                type: 'number'
            }

        ]

    },

    {
        id: 'media',

        title: 'Recursos',

        columns: 1,

        fields: [

            {
                model: 'profile_photo',
                label: 'Foto de perfil',
                type: 'url'
            }

        ]

    }

]