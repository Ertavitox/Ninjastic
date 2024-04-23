<template>
  <div>
    <div class="flex flex-col gap-6">
      <Modal :isModalOpen="isModalOpen" @close="isModalOpen = false">
        <template #title>
          <h2 class="mb-4 text-xl font-bold">Update Profile</h2>
        </template>
        <template #content>
          <form class="flex flex-col gap-2 mb-2">
            <input type="text" v-model="name" class="px-2 py-2 text-black rounded-lg focus:outline-none">
            <input type="text" v-model="username"
              class="px-2 py-2 text-black rounded-lg focus:outline-none">
            <input type="text" v-model="newPassword" placeholder="New Password" class="px-2 py-2 text-black rounded-lg focus:outline-none">
          </form>
        </template>
        <template #emojis></template>
        <template #button>
          <button @click.prevent="updateUserProfile" type="submit" class="px-4 py-2 my-1 rounded-lg bg-secondary">Update
            Profile</button>
        </template>
      </Modal>
      <h2 class="text-2xl font-bold">My Profile</h2>
      <div>
        <template v-if="!auth.userData?.username">
          <UserIcon class="w-auto h-12 p-2 bg-gray-500 rounded-full lg:mx-2"></UserIcon>
        </template>
        <template v-else>
          <div class="flex flex-row items-center gap-4">
            <GravatarImage :email="auth.userData?.username ?? ''" class="h-auto w-18"></GravatarImage>
            <a class="text-2xl font-bold uppercase">{{ auth.userData?.name }}</a>
          </div>
        </template>
        <div class="flex flex-col w-full">
          <h3 class="py-3 text-xs font-light">User Details</h3>
          <div class="inline-flex gap-4">
            <span class="w-16 text-gray-300/50">Name: </span>
            <span class="inline-flex items-center gap-2">{{ auth.userData?.name }}</span>
          </div>
          <div class="inline-flex gap-4">
            <span class="w-16 text-gray-300/50">Email: </span>
            <span class="inline-flex items-center gap-2">{{ auth.userData?.username }}</span>
          </div>
          <div class="inline-flex gap-4">
            <span class="w-16 text-gray-300/50">Password: </span>
            <span class="inline-flex items-center gap-2 mt-1"> *********** </span>
          </div>
          <button @click.prevent="toggleModal"
            class="max-w-full px-2 py-3 my-4 rounded-lg active:bg-secondary-700 hover:bg-secondary-400 sm:max-w-xs text-secondary-100 bg-secondary">Update
            Details</button>
          <div
            class="flex items-center px-4 py-2 my-2 space-x-1 text-sm text-blue-200 border-2 rounded-lg border-blue-200/20 bg-sky-500/20">
            <InformationCircleIcon class="w-auto h-8" />
            Do you want to change your avatar? <a href="https://gravatar.com/connect/"
              class="pr-1 font-medium underline" target="_blank">Register</a> and <a class="font-medium underline"
              href="https://gravatar.com/profile/avatars" target="_blank" rel="nofollow">change it here</a>.
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { ref, computed } from 'vue';
import { UserIcon } from '@heroicons/vue/24/solid';
import { InformationCircleIcon } from '@heroicons/vue/24/outline';
import GravatarImage from '@/components/GravatarImage.vue';
import { useAuthStore } from '@/stores/auth.store';
import { defineComponent } from 'vue';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import Modal from '@/components/ModalComp.vue';







export default defineComponent({
  components: {
    UserIcon,
    InformationCircleIcon,
    GravatarImage,
    Modal
  },
  setup() {
    const newPassword = ref('')
    const apiUrl = import.meta.env.VITE_API_URL;
    const auth = useAuthStore();
    const token = computed(() => auth.getToken());
    const isModalOpen = ref(false);
    const toggleModal = () => {
      isModalOpen.value = !isModalOpen.value;
    };

    const username = ref(auth.userData?.username ?? '');
    const name = ref(auth.userData?.name ?? '');
    const updateUserProfile = async () => {
      try {
        const userId = auth.userData?.user_id;
        if (!userId) {
          console.error('User ID not found.');
          return;
        }
        
        const payload = {
          name: name.value.toString(),
          email: username.value.toString(),
          password: newPassword.value.toString()
        };

        const response = await fetch(`${apiUrl}/users/${userId}`, {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token.value}`
          },
          body: JSON.stringify(payload)
        });
        console.log(response)
        if (response.ok) {
          const responseData = await response.json();
          toast.success(responseData.message, {
            position: toast.POSITION.BOTTOM_CENTER,
            theme: 'dark'
          });
        } else {
          toast.error('Failed to update profile', {
            position: toast.POSITION.BOTTOM_CENTER,
            theme: 'dark'
          });
        }
      } catch (error) {
        console.error('Error updating user profile:', error);
      }
    };

    return {
      auth,
      name,
      username,
      newPassword,
      toggleModal,
      isModalOpen,
      updateUserProfile
    };
  },
});
</script>
