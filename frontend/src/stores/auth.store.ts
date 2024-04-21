// auth.ts

import { defineStore } from 'pinia'
import { jwtDecode } from 'jwt-decode'
import { fetchWrapper } from '@/helpers/fetchWrapper'
import router from '@/router'
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const COOKIE_NAME = 'auth_data'

interface UserData {
  id: number
  token: string
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
    userData: null as UserData | null,
    returnUrl: null as string | null
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
    getUserData: (state) => () => state.userData,
    getToken: (state) => () => state.token
  },
  actions: {
    async register(email: string, name: string, password: string): Promise<void> {
      try {
        const response = await fetchWrapper.post(`${import.meta.env.VITE_API_URL}/users`, {
          email,
          name,
          password
        })
        const token = response.token
        if (token) {
          const decodedUserData: UserData = jwtDecode(token)
          this.setAuthData({ token, userData: decodedUserData })
          router.push(this.returnUrl || '/')
        } else {
          throw new Error('Invalid user data received from the server')
        }
      } catch (error) {
        console.error('Error during register:', error)
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
        if (token) {
          const decodedUserData: UserData = jwtDecode(token);
          this.setAuthData({ token, userData: decodedUserData });
          const destinationPath = this.returnUrl || '/';
          
          // Throw a toast notification indicating successful login
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
  

    setAuthData({ token, userData }: { token: string; userData: UserData }) {
      this.token = token
      this.userData = userData
      const authData = [{ token, userData }]
      const expiryDate = new Date(userData.exp * 1000); 
      document.cookie = `${COOKIE_NAME}=${JSON.stringify(authData)}; expires=${expiryDate.toUTCString()}; path=/`
    },

    clearAuthData() {
      this.token = null
      this.userData = null
      document.cookie = `${COOKIE_NAME}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`
    },

    async checkAuth() {
      const cookieValue = document.cookie
        .split('; ')
        .find((row) => row.startsWith(`${COOKIE_NAME}=`))
      if (cookieValue) {
        const authData = JSON.parse(cookieValue.split('=')[1])
        if (authData.length > 0) {
          const { token, userData } = authData[0]
          this.setAuthData({ token, userData })

          if (token) {
            const decodedUserData: UserData = jwtDecode(token) as UserData

            this.setUserData(decodedUserData)
          }
        }
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
