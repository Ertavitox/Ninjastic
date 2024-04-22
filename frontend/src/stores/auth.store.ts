// auth.ts

import { defineStore } from 'pinia'
import { jwtDecode } from 'jwt-decode'
import { fetchWrapper } from '@/helpers/fetchWrapper'
import router from '@/router'
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const COOKIE_NAME = 'auth_data'

export interface UserData {
  id: number
  token: string
  refresh_token: string
  iat: number
  exp: number
  roles: string[]
  username: string
  name: string
  user_id: number
}

export const useAuthStore = defineStore({
  id: 'auth',
  state: () => ({
    token: null as string | null,
    refresh_token: null as string | null,
    userData: null as UserData | null,
    returnUrl: null as string | null
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
    getUserData: (state) => () => state.userData,
    getToken: (state) => () => state.token,
    getRefreshToken: (state) => () => state.refresh_token
  },
  actions: {
    async register(email: string, name: string, password: string): Promise<void> {
      try {
        const response = await fetchWrapper.post(`${import.meta.env.VITE_API_URL}/users`, {
          email,
          name,
          password
        });
        
        if (response) {
          const responseData = response;
          if (responseData.user) {
            toast.success('Registered! You may login now', {
              position: toast.POSITION.BOTTOM_CENTER,
              autoClose: 3000,
              theme: 'dark'
            });
    
            const destinationPath = '/login';
            setTimeout(() => {
              router.push({ path: destinationPath, query: router.currentRoute.value.query });
            }, 3000); 
          } else {
            throw new Error('Invalid user data received from the server');
          }
        } else {
          throw new Error('User registration failed');
        }
      } catch (error) {
        console.error('Error during register:', error);
        throw error; 
      }
    },    
    async login(username: string, password: string): Promise<void> {
      try {
        const response = await fetchWrapper.post(`${import.meta.env.VITE_API_URL}/login`, {
          username,
          password
        });
        const token = response.token;
        const refreshToken = response.refresh_token;
        if (token) {
          const decodedUserData: UserData = jwtDecode(token);
          this.setAuthData({ token, refreshToken, userData: decodedUserData });
          const destinationPath = this.returnUrl || '/';
          
          toast.success('Logged in successfully! Redirecting...', {
            position: toast.POSITION.BOTTOM_CENTER,
            autoClose: 3000,
            theme: 'dark'
          });
    
          setTimeout(() => {
            router.push({ path: destinationPath, query: router.currentRoute.value.query });
          }, 3000); 
        } else {
          throw new Error('Invalid user data received from the server');
        }
      } catch (error) {
        console.error('Error during login:', error);
        throw error;
      }
    },

    async refreshAuthToken(newRefresh: string): Promise<void> {
      try {
        if (!this.refresh_token) {
          throw new Error('Refresh token is not available');
        }

        const response = await fetchWrapper.post(`${import.meta.env.VITE_API_URL}/token/refresh`, {
          refreshToken: newRefresh
        });

        if (response.token) {
          const token = response.token;
          const decodedUserData: UserData = jwtDecode(token);
          this.setAuthData({ token, refreshToken: response.refresh_token, userData: decodedUserData });
        } else {
          this.logout();
        }
      } catch (error) {
        console.error('Error refreshing token:', error);
        this.logout();
      }
    },

    setAuthData({ token, refreshToken, userData }: { token: string; refreshToken?: string, userData: UserData }) {
      this.token = token
      this.refresh_token = refreshToken || ''; // Ensure refreshToken is always treated as string
      this.userData = userData
      const authData = [{ token, refreshToken, userData }]
      const expiryDate = new Date(userData.exp * 1000); 
      document.cookie = `${COOKIE_NAME}=${JSON.stringify(authData)}; expires=${expiryDate.toUTCString()}; path=/`
    },

    clearAuthData() {
      this.token = null
      this.refresh_token = null
      this.userData = null
      document.cookie = `${COOKIE_NAME}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`
    },

    async checkAuth() {
      const currentTime = Math.floor(Date.now() / 1000);
      const cookieValue = document.cookie
        .split('; ')
        .find((row) => row.startsWith(`${COOKIE_NAME}=`))
      if (cookieValue) {
        const authData = JSON.parse(cookieValue.split('=')[1])
        if (authData.length > 0) {
          const { token, refreshToken, userData } = authData[0]
          this.setAuthData({ token, refreshToken, userData })
    
          if (token) {
            const decodedUserData: UserData = jwtDecode(token) as UserData
            if (decodedUserData.exp < currentTime && refreshToken) {
              await this.refreshAuthToken(refreshToken);
            }
            this.setUserData(decodedUserData)
          }
        }
      } else {
        this.logout();
      }
    },

    setUserData(userData: UserData) {
      this.userData = userData
    },

    logout() {
      this.clearAuthData()
      router.push("/login")
    },

    handleAuthError(returnUrl: string) {
      this.clearAuthData()
      this.returnUrl = returnUrl || '/login'
      window.location.href = this.returnUrl
    }
  }
})
