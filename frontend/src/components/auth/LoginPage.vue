<script lang="ts">
import { defineComponent } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from './authStore';

export default defineComponent({
  data() {
    return {
      email: '',
      password: '',
      errorMessage: '',
      apiUrl: `${import.meta.env.VITE_API_URL}`,
      authStore: useAuthStore(),
      router: useRouter()
    };
  },
  methods: {
    async loginUser() {
      try {
        const response = await fetch(`${this.apiUrl}/login`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ username: this.email, password: this.password })
        });

        if (response.ok) {
          const responseData = await response.json();
          const userDetails = {
          token: responseData.token
          //todo more data...
        };
        this.authStore.login(userDetails);
        this.router.push('/discussions');
        } else {
          const responseData = await response.json();
          this.errorMessage = responseData.message;
        }
      } catch (error) {
        console.error('Error:', error);
      }
    }
  }
});
</script>

<template>
    <div class="flex items-center max-w-lg px-4 py-20 mx-auto my-8 bg-gray-800 shadow-md rounded-xl">
      <div class="flex flex-col justify-center w-full text-center">
        <div id="logo" class="flex self-center my-2">
          <div class="relative text-6xl font-black select-none rotate-6 text-gray-950/60">N
            <div class="absolute top-0 left-0 right-0 w-12 h-3 my-5 bg-black rounded-md">
              <div class="absolute h-2 w-3 bg-primary rounded-l-md rounded-r-md my-0.5 mx-2"></div>
              <div class="absolute top-0 right-0 h-2 w-3 rounded-l-md rounded-r-md bg-primary my-0.5 mx-2"></div>
            </div>
          </div>
        </div>
        <div class="text-3xl"><span class="antialiased font-bold">Ninjastic</span> Login</div>
        <form @submit.prevent="loginUser" method="post" class="flex flex-col items-center p-8 mt-8">
          <div class="flex flex-col w-full gap-8">
            <div class="flex flex-col gap-2">
              <input placeholder="Email Address" v-model="email"
                class="flex-grow w-full py-2 pl-4 text-black rounded-lg focus:outline-none" type="text"
                name="email" id="email" autocomplete="email" autofocus />
              <input placeholder="Password" v-model="password"
                class="flex-grow w-full py-2 pl-4 text-black rounded-lg focus:outline-none" type="password"
                name="password" id="password" autocomplete="current-password" />
            </div>
            <div v-if="errorMessage" class="py-2 font-semibold border rounded-lg text-primary border-primary bg-primary/10">{{ errorMessage }}</div>
            <button class="w-full py-2 text-white bg-green-500 rounded-lg" type="submit">Login</button>
          </div>
        </form>
      </div>
    </div>
  </template>