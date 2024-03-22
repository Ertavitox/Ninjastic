<script lang="ts">
import { defineComponent } from 'vue';


export default defineComponent({
  data() {
    return {
      email: '',
      password: '',
      apiUrl: `${import.meta.env.VITE_API_URL}`

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
          console.log('Login successful');
        } else {
          console.error('Login failed');
        }
      } catch (error) {
        console.error('Error:', error);
      }
    }
  }
});
</script>

<template>
    <div class="flex py-20 px-4 bg-gray-800 my-8 rounded-xl shadow-md items-center max-w-lg mx-auto">
      <div class="text-center flex justify-center flex-col w-full">
        <div id="logo" class="my-2 flex self-center">
          <div class="text-6xl font-black rotate-6 text-gray-950/60 select-none relative">N
            <div class="absolute top-0 my-5 rounded-md left-0 right-0 bg-black w-12 h-3">
              <div class="absolute h-2 w-3 bg-red-500 rounded-l-md rounded-r-md my-0.5 mx-2"></div>
              <div class="absolute top-0 right-0 h-2 w-3 rounded-l-md rounded-r-md bg-red-500 my-0.5 mx-2"></div>
            </div>
          </div>
        </div>
        <div class="text-3xl"><span class="font-bold antialiased">Ninjastic</span> Login</div>
        <form @submit.prevent="loginUser" method="post" class="flex items-center flex-col mt-8 p-8">
          <div class="flex flex-col w-full gap-8">
            <div class="gap-2 flex flex-col">
              <input placeholder="Email Address" v-model="email"
                class="text-black focus:outline-none pl-4 py-2 w-full rounded-lg flex-grow" type="text"
                name="email" id="email" autocomplete="email" autofocus />
              <input placeholder="Password" v-model="password"
                class="text-black focus:outline-none pl-4 py-2 w-full rounded-lg flex-grow" type="password"
                name="password" id="password" autocomplete="current-password" />
            </div>
            <button class="text-white py-2 rounded-lg bg-green-500 w-full" type="submit">Login</button>
          </div>
        </form>
      </div>
    </div>
  </template>