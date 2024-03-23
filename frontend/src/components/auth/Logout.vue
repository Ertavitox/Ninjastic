<template>
    <div v-if="authStore.isAuthenticated">
    <!-- Authenticated content -->
    <button @click="logoutUser">Logout</button>
  </div>
  <div v-else>
    <!-- Login form -->

  </div>
</template>
<script lang="ts">
import { defineComponent } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from './authStore';


export default defineComponent({
    data() {
        return {
            authStore: useAuthStore(),
            router: useRouter()
        }
    },
    methods: {
        logoutUser() {
            this.authStore.logout()
        } 
    }

})
</script>
