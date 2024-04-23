<script setup lang="ts">
import { ref, computed, onMounted, inject } from 'vue';
import { RouterLink, RouterView, useRouter } from 'vue-router'
import { ArrowRightStartOnRectangleIcon, Bars3Icon, HomeIcon as HomeIconOutlined, PlusCircleIcon } from '@heroicons/vue/24/outline'
import { FireIcon as FireOutlined } from '@heroicons/vue/24/outline'
import { FireIcon as FireSolid } from '@heroicons/vue/24/solid'
import { HomeIcon as HomeIconFilled } from '@heroicons/vue/24/solid'
import { XMarkIcon } from '@heroicons/vue/24/solid'
import Modal from './components/ModalComp.vue';
import GravatarImage from './components/GravatarImage.vue'
import { useAuthStore } from '@/stores/auth.store'
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const easterActive = ref(false);
const router = useRouter()
const auth = useAuthStore();

const config = ref(JSON.parse(localStorage.getItem('config') || '{}') || {
  sidebarOpen: false,
});

const showDropdown = ref(false);
const threadTitle = ref('');
const threadDescription = ref('');
const firstComment = ref('');
const isOpen = computed(() => config.value.sidebarOpen);
const isModalOpen = ref(false);
const toggleModal = () => {
  console.log(isModalOpen.value)
  isModalOpen.value = !isModalOpen.value;
};
const toggleSidebar = () => {
  config.value.sidebarOpen = !config.value.sidebarOpen;
  localStorage.setItem('config', JSON.stringify(config.value));

};

interface Smileys {
  [key: string]: string[];
}
const insertEmojiIntoTextarea = (emoji: string) => {
  firstComment.value += emoji;
};
const smileys = inject<Smileys>('emojis', {
  '1': ['😀', '😁', '😂', '😃', '😄', '😅'],
  '2': ['😆', '😇', '😈', '😉', '😊', '😋'],
  '3': ['😌', '😍', '😎', '😏', '😐', '😑'],
  '4': ['😒', '😓', '😔', '😕', '😖', '😗']
});

const logout = () => {
  auth.logout()
}

const currentRouteName = computed(() => router.currentRoute.value.name);

const newThread = async () => {
  try {
    const thread = {
      name: threadTitle.value,
      description: threadDescription.value
    };

    const response = await fetch(`${import.meta.env.VITE_API_URL}/topics`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${auth.token}`
      },
      body: JSON.stringify(thread)
    });
    const responseData = await response.json();
    if (response.ok) {
      const threadId = responseData.result.id;
      await newComment(threadId);
      toast.success(responseData.message, {
        autoClose: 3000,
        position: toast.POSITION.BOTTOM_CENTER,
        theme: 'dark'
      });
      window.location.href = `/discussions/thread/${threadId}`;
      toggleModal();
    } else {
      toast.error(responseData.message, {
        autoClose: 3000,
        position: toast.POSITION.BOTTOM_CENTER,
        theme: 'dark'
      });
    }
  } catch (error) {
    console.error('Error creating thread:', error);
  }
};

const newComment = async (threadId: number) => {
  const firstCommentwithoutEmoji = (firstComment.value as string) ?
    (firstComment.value as string).replace(/[\u{1F600}-\u{1F64F}]/gu, (match: string | undefined) => {
      if (match) {
        return `\\u{${match.codePointAt(0)!.toString(16)}}`;
      }
      return '';
    }) :
    '';
  try {
    const response = await fetch(`${import.meta.env.VITE_API_URL}/topics/${threadId}/comments`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${auth.token}`
      },
      body: JSON.stringify({
        original: firstCommentwithoutEmoji
      })
    });
    if (response.ok) {
      // Since this is a first thread, we don't need notification.
    } else {
      // Since this is a first thread, we don't need notification.
    }
  } catch (error) {
    console.error('Error creating comment:', error);
  }
};



const easterEgg = () => {
  easterActive.value = !easterActive.value;
  const leftEye = document.getElementById('left');
  const rightEye = document.getElementById('right');

  if (leftEye && rightEye) {
    leftEye.classList.add('animate-pulse-faster', 'fill-red-500');

    rightEye.classList.add('animate-pulse', 'fill-blue-500');
  }
}

const homeURL = () => {
  router.push("/")
}

const openDropdown = () => {
  showDropdown.value = true;
};

const isMobileMenuOpen = ref(false);

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const closeDropdown = () => {
  showDropdown.value = false;
};

onMounted(async () => {
  localStorage.setItem('config', JSON.stringify(config.value));

});


</script>

<template>


  <div class="flex flex-col md:flex-row ">
    <transition name="slide-fade">
      <div v-if="isMobileMenuOpen" class="fixed inset-0 z-50 bg-gray-800/80 backdrop-blur-sm">
        <div class="flex flex-col items-center justify-center h-full">
          <nav class="text-white">
            <ul class="flex flex-col gap-6 text-xl text-center">
              <li>
                <RouterLink to="/">Home</RouterLink>
              </li>
              <li>
                <RouterLink to="/hot-topics">Hot Topics</RouterLink>
              </li>
            </ul>
          </nav>
          <!-- Close button -->
          <button @click="toggleMobileMenu" class="absolute text-white top-4 right-4">
            <XMarkIcon class="w-12 h-12"></XMarkIcon>
          </button>
        </div>
      </div>
    </transition>
    <!-- Navbar Desktop -->
    <div :class="{ 'w-28 text-center': !isOpen, 'w-80 text-left': isOpen }"
      class="hidden h-screen text-white transition-all lg:bg-gray-800 md:fixed lg:block">
      <nav class="z-50 py-2">
        <div id="logo-container" @click="homeURL" class="flex items-center justify-center cursor-pointer">
          <svg @click="easterEgg" class="w-auto h-20 mt-4 text-gray-900" viewBox="0 0 131 152" fill="none"
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
          <div class="mt-2 text-2xl font-bold text-gray-500" v-if="isOpen">
            Ninjastic
          </div>
        </div>

        <ul :class="{ 'items-center justify-center': !isOpen, 'p-5': isOpen }" class="flex flex-col gap-6 pt-10">
          <RouterLink to="/" v-slot="{ isExactActive }">
            <li>
              <template v-if="isExactActive">
                <div :class="{ 'bg-gray-900 ': isOpen }"
                  class="flex items-center gap-4 p-2 px-4 text-lg cursor-default rounded-xl">
                  <HomeIconFilled class="w-8 h-8"></HomeIconFilled>
                  <span v-if="isOpen">Home</span>
                </div>
              </template>
              <template v-else>
                <div class="flex items-center gap-4 p-2 px-4 text-lg cursor-pointer">
                  <HomeIconOutlined class="w-8 h-8 hover:stroke-red-500 "></HomeIconOutlined>
                  <span v-if="isOpen">Home</span>
                </div>
              </template>
            </li>
          </RouterLink>

          <RouterLink to="/hot-topics" v-slot="{ isExactActive }">
            <li>
              <template v-if="isExactActive">
                <div :class="{ 'bg-gray-900 ': isOpen }"
                  class="flex items-center gap-4 p-2 px-4 text-lg rounded-lg cursor-pointer">
                  <FireSolid class="w-8 h-8"></FireSolid>
                  <span v-if="isOpen">Hot Topics</span>
                </div>
              </template>
              <template v-else>
                <div class="flex items-center gap-4 p-2 px-4 text-lg cursor-pointer">
                  <FireOutlined class="w-8 h-8 hover:stroke-red-500"></FireOutlined>
                  <span v-if="isOpen">Hot Topics</span>
                </div>
              </template>
            </li>
          </RouterLink>
        </ul>
      </nav>

    </div>

    <!-- Main Content -->
    <div :class="{ 'mx-4 lg:ml-28 ': !isOpen, 'lg:ml-80': isOpen }" class="relative flex-1 transition-all ">
      <div class="z-0 flex-1 m-8 bg-gray-900 lg:m-12">
        <div class="gap-8">
          <!-- Search Bar -->
          <div id="action-bar" class="flex flex-col-reverse justify-between gap-12 lg:flex-row">
            <div class="flex items-center gap-4">
              <ArrowRightStartOnRectangleIcon @click="toggleSidebar()" class="hidden w-8 h-8 lg:block">
              </ArrowRightStartOnRectangleIcon>
             
            </div>

            <div class="flex items-center justify-around">
              <div>
                <Bars3Icon @click="toggleMobileMenu" class="w-12 h-12 lg:hidden"></Bars3Icon>
              </div>
              <div class="flex items-center justify-end w-full gap-8">
                <div v-if="auth.userData">
                  <PlusCircleIcon @click="toggleModal()" class="h-10 transition-colors hover:text-secondary">
                  </PlusCircleIcon>
                </div>

                <div class="relative">
                  <!-- Dropdown button -->
                  <div @click="openDropdown" class="cursor-pointer">
                    <GravatarImage :email="auth.userData?.username ?? ''" class="w-12 h-auto"></GravatarImage>
                  </div>
                  <div v-if="showDropdown" id="dropdown" @click="closeDropdown" @mouseleave="closeDropdown"
                    class="absolute right-0 z-10 w-56 mt-2 border rounded-lg shadow-lg bg-gray-950 border-gray-950">
                    <div class="text-gray-500 ">

                      <template v-if="auth.userData">
                        <div
                          class="flex flex-col px-4 py-2 text-sm text-right text-white rounded-t-lg cursor-pointer bg-secondary">
                          <span>{{ auth.userData?.name }}</span>
                          <span class="text-xs font-bold text-secondary-900/80">{{ auth.userData?.username }}</span>
                        </div>
                        <a href="/my-profile">
                          <div class="px-4 py-2 text-sm cursor-pointer hover:bg-gray-100 hover:text-gray-900">
                            <a>My Profile</a>
                          </div>
                        </a>
                        <div class="px-4 py-2 text-sm cursor-pointer hover:bg-gray-100 hover:text-gray-900"
                          @click="logout">
                          <a>Logout</a>
                        </div>
                      </template>
                      <div v-if="!auth.userData"
                        class="px-4 py-2 text-sm rounded-lg cursor-pointer hover:bg-gray-100 hover:text-gray-900">
                        <a href="/login">Login</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Breadcrumb -->
          <div id="breadcrumb" class="mx-2 my-5 font-semibold text-gray-500">Forum > {{ currentRouteName }} </div>

          <div class="flex flex-col xl:flex-row">
            <div class="flex-1">
              <IntroBox class="hidden" />
            </div>
          </div>
        </div>
        <Modal :isModalOpen="isModalOpen" @close="isModalOpen = false" :emojis="smileys"
          @insert-emoji="insertEmojiIntoTextarea">
          <template #title>
            <h2 class="mb-4 text-xl font-bold">New Conversation</h2>
          </template>
          <template #content>
            <form class="flex flex-col">
              <input v-model="threadTitle" type="text" class="px-4 py-2 my-1 text-black rounded-lg focus:outline-none"
                placeholder="Subject" />
              <input v-model="threadDescription" class="px-4 py-2 my-1 text-black rounded-lg focus:outline-none"
                placeholder="Description" />
              <textarea v-model="firstComment" class="h-32 px-4 py-2 my-1 text-black rounded-lg focus:outline-none"
                placeholder="Start a conversation with something">
              </textarea>
            </form>
          </template>
          <template #emojis></template>
          <template #button>
            <button @click.prevent="newThread" type="submit" class="px-4 py-2 my-1 rounded-lg bg-secondary">Create
              Topic</button>
          </template>
        </Modal>
        <div>
          <RouterView></RouterView>
        </div>
      </div>
    </div>
  </div>
</template>
