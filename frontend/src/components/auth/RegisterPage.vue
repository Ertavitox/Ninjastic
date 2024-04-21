<script lang="ts">
import { defineComponent } from 'vue';
import { useAuthStore } from '@/stores/auth.store';

interface User {
  username: string;
  name: string;
  password: string;
}

export default defineComponent({
  data() {
    return {
      username: '',
      name: '',
      password: '',
      errorMessage: '',

    };
  },
  methods: {
    registerUser(values: User): void {
      const authStore = useAuthStore();
      const { username, name, password } = values;
      authStore.register(username, name, password)
        .catch(error => {
        this.errorMessage = error;
        });
    },

    handleSubmit(event: Event) {
      event.preventDefault();
      const { username, name, password } = this;
      this.registerUser({ username, name, password });
    }
  }
});
</script>

<template>
  <div class="flex items-center w-full max-w-sm px-4 py-20 mx-auto my-8 bg-gray-800 shadow-md rounded-xl">
    <div class="flex flex-col justify-center w-full text-center">
      <div id="logo-container" class="flex items-center justify-center">
        <svg class="w-auto h-20 mt-4 text-gray-900" viewBox="0 0 131 152" fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path
            d="M30.014 120C29.1607 120 28.3927 119.701 27.71 119.104C27.0273 118.421 26.686 117.611 26.686 116.672V33.728C26.686 32.7893 27.0273 32.0213 27.71 31.424C28.3927 30.7413 29.1607 30.4 30.014 30.4H47.038C48.574 30.4 49.6833 30.784 50.366 31.552C51.0487 32.2347 51.518 32.7467 51.774 33.088L78.142 75.456V33.728C78.142 32.7893 78.4833 32.0213 79.166 31.424C79.8487 30.7413 80.6167 30.4 81.47 30.4H100.798C101.737 30.4 102.505 30.7413 103.102 31.424C103.785 32.0213 104.126 32.7893 104.126 33.728V116.672C104.126 117.611 103.785 118.421 103.102 119.104C102.505 119.701 101.737 120 100.798 120H83.902C82.2807 120 81.1287 119.659 80.446 118.976C79.7633 118.208 79.294 117.653 79.038 117.312L52.67 77.376V116.672C52.67 117.611 52.3287 118.421 51.646 119.104C51.0487 119.701 50.2807 120 49.342 120H30.014Z"
            fill="currentColor" />
          <rect id="ninja" class="group" x="16" y="48" width="96" height="34" rx="12" fill="black"
            fill-opacity="0.95" />
          <rect id="left" class="group" x="28" y="55" width="31" height="20" rx="10" fill="#CC3C3C" />
          <path id="right" class="group"
            d="M70 65C70 59.4772 74.4772 55 80 55H91C96.5228 55 101 59.4772 101 65V65C101 70.5228 96.5228 75 91 75H80C74.4772 75 70 70.5228 70 65V65Z"
            fill="#CC3C3C" />
        </svg>

      </div>
      <div class="text-3xl"><span class="antialiased font-bold">Ninjastic</span> Registration</div>
      <form @submit.prevent="handleSubmit" method="post" class="flex flex-col items-center p-8 mt-8">
        <div class="flex flex-col w-full gap-8">
          <div class="flex flex-col gap-2">
            <input placeholder="Email Address" v-model="username"
              class="flex-grow w-full py-2 pl-4 text-black placeholder-gray-500 rounded-lg focus:outline-none"
              type="text" name="email" id="email" autocomplete="email" autofocus />
            <input placeholder="Your Name" v-model="name"
              class="flex-grow w-full py-2 pl-4 text-black placeholder-gray-500 rounded-lg focus:outline-none"
              type="text" name="name" id="name" autocomplete="name" autofocus />
            <input placeholder="Password" v-model="password"
              class="flex-grow w-full py-2 pl-4 text-black placeholder-gray-500 rounded-lg focus:outline-none"
              type="password" name="password" id="password" autocomplete="current-password" />
          </div>
          <div v-if="errorMessage"
            class="py-2 font-semibold border rounded-lg text-primary border-primary bg-primary/10">{{ errorMessage }}
          </div>
          <button class="w-full py-2 text-white bg-green-500 rounded-lg" type="submit">Register</button>
          <div>
            <span class="text-gray-200">Already Registered? <a href="/login" class="text-blue-500">Login here</a></span>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>