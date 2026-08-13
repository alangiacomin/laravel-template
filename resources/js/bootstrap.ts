import axios from 'axios';
import Cookies from 'js-cookie';

axios.defaults.withCredentials = true;
axios.defaults.xsrfCookieName = 'XSRF-TOKEN';
axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

axios.interceptors.request.use(config => {
    let token = Cookies.get('XSRF-TOKEN');

    if (!token) {
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) {
            token = meta.getAttribute('content') ?? undefined;
        }
    }

    if (token) {
        config.headers['X-XSRF-TOKEN'] = decodeURIComponent(token);
    }

    return config;
});


axios.interceptors.response.use(null, async error => {
    if (error.response?.status === 419 && !error.config._retry) {
        error.config._retry = true;
        await axios.get('/sanctum/csrf-cookie');
        return axios.request(error.config);
    }
    return Promise.reject(error);
});

// OpenAPI.BASE = import.meta.env.VITE_BASE_URL || 'http://localhost:8000';
