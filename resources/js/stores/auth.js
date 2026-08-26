import { defineStore } from 'pinia';
import axios from '../bootstrap';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        loaded: false,
        settings: null,
    }),

    getters: {
        isLoggedIn: (s) => !!s.user,
        isAdmin: (s) => !!s.user?.is_admin,
    },

    actions: {
        async fetchUser() {
            try {
                const { data } = await axios.get('/api/me');
                this.user = data.user;
            } catch {
                this.user = null;
            } finally {
                this.loaded = true;
            }
        },

        async fetchSettings() {
            try {
                const { data } = await axios.get('/api/settings');
                this.settings = data;
            } catch {
                this.settings = null;
            }
        },

        async logout() {
            await axios.post('/api/auth/logout');
            this.user = null;
        },
    },
});
