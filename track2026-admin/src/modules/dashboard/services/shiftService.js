import api from '@/services/api'

const shiftService = {

    async active() {
        const { data } = await api.get('/shifts/active')
        return data.data
    },

    async start(performanceId) {
        const { data } = await api.post(`/shifts/${performanceId}/start`)
        return data.data
    },

    async pause(shiftId) {
        const { data } = await api.post(`/shifts/${shiftId}/pause`)
        return data.data
    },

    async resume(shiftId) {
        const { data } = await api.post(`/shifts/${shiftId}/resume`)
        return data.data
    },

    async finish(shiftId) {
        const { data } = await api.post(`/shifts/${shiftId}/finish`)
        return data.data
    },

}

export default shiftService