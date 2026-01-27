import axios from 'axios'

const api = axios.create({
    baseURL: '/api',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
    },
    withCredentials: true, // important for Sanctum later
})

export default api
