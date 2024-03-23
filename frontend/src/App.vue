<script setup lang="ts">
import { ref, computed } from 'vue';
import { RouterLink, RouterView, useRouter } from 'vue-router'
import { ArrowRightStartOnRectangleIcon, Bars3Icon, HomeIcon as HomeIconOutlined } from '@heroicons/vue/24/outline'
import { ChatBubbleBottomCenterTextIcon as ChatOutlined } from '@heroicons/vue/24/outline'
import { ChatBubbleBottomCenterTextIcon as ChatSolid, MagnifyingGlassIcon } from '@heroicons/vue/24/solid'
import { FireIcon as FireOutlined } from '@heroicons/vue/24/outline'
import { FireIcon as FireSolid } from '@heroicons/vue/24/solid'
import { PlusIcon } from '@heroicons/vue/24/outline'
import { BellIcon } from '@heroicons/vue/24/outline'
import { UserIcon } from '@heroicons/vue/24/solid'
import { HomeIcon as HomeIconFilled } from '@heroicons/vue/24/solid'
import IntroBox from './components/IntroBox.vue'
import StaffOnline from './components/StaffOnline.vue'

const isOpen = ref(false);
const easterActive = ref(false);
const router = useRouter()

const toggleSidebar = () => {
  isOpen.value = !isOpen.value;
};




const currentRouteName = computed(() => router.currentRoute.value.name);

const easterEgg = () => {
  easterActive.value = !easterActive.value;
  const leftEye = document.getElementById('left');
  const rightEye = document.getElementById('right');

  if (leftEye && rightEye) {
    leftEye.classList.add('animate-pulse');
    rightEye.classList.add('animate-pulse');
  }
}

</script>

<template>
  <div class="flex flex-col h-screen md:flex-row">
    <!-- Navbar Desktop -->
    <div :class="{ 'w-28 text-center': !isOpen, 'w-80 text-left': isOpen }"
      class="hidden text-white transition-all bg-gray-800 md:block ">
      <nav class="z-50 py-2">
        <div id="logo-container" class="flex items-center justify-center">
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
          <RouterLink to="/discussions" v-slot="{ isExactActive }">
            <li>
              <template v-if="isExactActive">
                <div :class="{ 'bg-gray-900 ': isOpen }"
                  class="flex items-center gap-4 p-2 px-4 text-lg rounded-lg cursor-pointer">
                  <ChatSolid class="w-8 h-8"></ChatSolid>
                  <span v-if="isOpen">Discussions</span>
                </div>
              </template>
              <template v-else>
                <div class="flex items-center gap-4 p-2 px-4 text-lg cursor-pointer">
                  <ChatOutlined class="w-8 h-8 hover:stroke-red-500"></ChatOutlined>
                  <span v-if="isOpen">Discussions</span>
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
    <div class="z-0 flex-1 m-8 bg-gray-900 lg:m-12">
      <div class="gap-8">
        <!-- Search Bar -->
        <div id="action-bar" class="flex flex-col-reverse justify-between w-full gap-12 lg:flex-row">
          <div class="flex items-center gap-4">
            <ArrowRightStartOnRectangleIcon @click="toggleSidebar()" class="hidden w-8 h-8 lg:block">
            </ArrowRightStartOnRectangleIcon>
            <div class="relative flex w-full max-w-lg hi">
              <input type="text" class="w-full py-4 pl-12 bg-gray-700 min-w-96 focus:outline-none rounded-2xl">
              <MagnifyingGlassIcon class="absolute top-0 left-0 w-auto h-8 m-3"></MagnifyingGlassIcon>
            </div>
          </div>

          <div class="flex items-center justify-around">
            <div>
              <Bars3Icon class="w-12 h-12 lg:hidden"></Bars3Icon>
            </div>
            <div class="flex items-center justify-end w-full gap-8">
              <PlusIcon class="h-8 p-1 border-2 rounded-full "></PlusIcon>
              <BellIcon class="h-10"></BellIcon>
              <UserIcon class="h-12 p-1 bg-gray-700 hover:bg-gray-600 rounded-xl"></UserIcon>
            </div>

          </div>
        </div>
        <!-- Breadcrumb -->
        <div id="breadcrumb" class="mx-2 my-5 font-semibold text-gray-500">Forum > {{ currentRouteName }} </div>

        <!-- IntroBox and Staff Online -->
        <div class="flex flex-col xl:flex-row">
          <div class="flex-1">
            <IntroBox class="hidden" />
          </div>
          <StaffOnline class="hidden lg:mx-16" />
        </div>
      </div>

      <!-- Content area -->
      <div>
        <!-- Your content goes here -->
        <RouterView></RouterView>
      </div>
    </div>

  </div>
</template>
