import { defineStore } from 'pinia';

interface UserDetails {
  username: string;
  token: string;
}

export const useAuthStore = defineStore('auth', {
  state: () => {
    const storedUser = localStorage.getItem('user');
    return storedUser ? JSON.parse(storedUser) : null;
  },
  getters: {
    isAuthenticated(state): boolean {
      return !!state;
    },
    token(state): string {
      return state ? state.token : '';
    },
    username(state): string {
      return state ? state.username : '';
    }
  },
  actions: {
    login(userDetails: UserDetails) {
      localStorage.setItem('user', JSON.stringify(userDetails));
    },
    logout() {
      localStorage.removeItem('user');
      this.$patch(null);
    }
  }
});
