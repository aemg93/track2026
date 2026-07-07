import Models from '@/modules/performances/pages/Models.vue'
import ModelShow from '@/modules/performances/pages/ModelShow.vue'
import ModelEdit from '@/modules/performances/pages/ModelEdit.vue'
import ModelCreate from '@/modules/performances/pages/ModelCreate.vue'

const performancesRoutes = [
    {
        path: 'performances',
        name: 'performances.index',
        component: Models,
    },
    {
        path: 'performances/create',
        name: 'performances.create',
        component: ModelCreate,
    },
    {
        path: 'performances/:id',
        name: 'performances.show',
        component: ModelShow,
        props: true,
    },
    {
        path: 'performances/:id/edit',
        name: 'performances.edit',
        component: ModelEdit,
        props: true,
    },
]

export default performancesRoutes