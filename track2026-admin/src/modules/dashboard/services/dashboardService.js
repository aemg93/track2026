import api from '@/services/api'

const dashboardService = {

    async index() {
        const { data } = await api.get('/dashboard')
        return data.data
    },

}

export default dashboardService