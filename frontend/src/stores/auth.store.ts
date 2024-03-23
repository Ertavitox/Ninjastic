import { defineStore } from 'pinia';
import { fetchWrapper } from '@/helpers/fetchWrapper';
import router from '@/router';

const baseUrl = `${import.meta.env.VITE_API_URL}`;

interface User {
    id: number;
    username: string;
    token: string;
}

export const useAuthStore = defineStore({
    id: 'auth',
    state: () => ({
        user: JSON.parse(localStorage.getItem('user') || '{}') as User | null,
        returnUrl: null as string | null // Specify the type explicitly
    }),
    actions: {
        async login(username: string, password: string): Promise<void> {
            try {
                const response = await fetchWrapper.post(`${baseUrl}/login`, { username, password });
                const userData = response as User;
                if (userData.token) {
                    this.user = userData;
                    localStorage.setItem('user', JSON.stringify(userData));
                    router.push(this.returnUrl || '/');
                } else {
                    throw new Error('Invalid user data received from the server');
                }
            } catch (error) {
                
                console.error('Error during login:', error);
            }
        },
        logout(): void {
            this.user = null;
            localStorage.removeItem('user');
            router.push('/login');
        }
    }
});
