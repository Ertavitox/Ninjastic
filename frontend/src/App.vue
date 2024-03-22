<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
import { HomeIcon as HomeIconOutlined } from '@heroicons/vue/24/outline'
import { ChatBubbleBottomCenterTextIcon as ChatOutlined } from '@heroicons/vue/24/outline'
import { ChatBubbleBottomCenterTextIcon as ChatSolid } from '@heroicons/vue/24/solid'
import { FireIcon as FireOutlined } from '@heroicons/vue/24/outline'
import { FireIcon as FireSolid } from '@heroicons/vue/24/solid'
import { PlusIcon } from '@heroicons/vue/24/outline'
import { BellIcon } from '@heroicons/vue/24/outline'
import { UserIcon } from '@heroicons/vue/24/solid'
import { HomeIcon as HomeIconFilled } from '@heroicons/vue/24/solid'
import IntroBox from './components/IntroBox.vue'
import StaffOnline from './components/StaffOnline.vue'

</script>

<template>
  <div class="flex flex-col md:flex-row h-screen">
    <!-- Navbar Desktop -->
    <div class="hidden md:block w-28 bg-gray-800 text-center text-white">
      <nav class="py-2">
        <div id="logo" class="my-8 ">
          <div class="text-6xl font-black rotate-6 text-gray-950/60 select-none relative">N
            <div class="absolute top-0 my-5 mx-8 rounded-md left-0 right-0 bg-black w-12 h-3">
              <div class="absolute h-2 w-3 bg-red-500 rounded-l-md rounded-r-md my-0.5 mx-2  "></div>
              <div class="absolute top-0 right-0 h-2 w-3 rounded-l-md rounded-r-md bg-red-500 my-0.5 mx-2"></div>
            </div>
          </div>
        </div>
        <ul class="flex items-center justify-center flex-col gap-12 pt-10">
          <RouterLink to="/" v-slot="{ isExactActive }">
            <li class="cursor-pointer">
              <template v-if="isExactActive">
                <HomeIconFilled class="w-8 h-8"></HomeIconFilled>
              </template>
              <template v-else>
                <HomeIconOutlined class="w-8 h-8 hover:stroke-red-500 "></HomeIconOutlined>
              </template>
            </li>
          </RouterLink>
          <RouterLink to="/discussions" v-slot="{ isExactActive }">
            <li class="relative cursor-pointer ">
              <template v-if="isExactActive">
                <ChatSolid class="w-8 h-8"></ChatSolid>
              </template>
              <template v-else>
                <ChatOutlined class="w-8 h-8 hover:stroke-red-500"></ChatOutlined>
              </template>
              <div class="absolute -m-1 h-3 w-3 bg-red-500 rounded-full right-0 top-0"></div>
            </li>
          </RouterLink>
          <RouterLink to="/hot-topics" v-slot="{ isExactActive }">
            <li class="cursor-pointer ">
              <template v-if="isExactActive">
                <FireSolid class="w-8 h-8"></FireSolid>
              </template>
              <template v-else>
                <FireOutlined class="w-8 h-8 hover:stroke-red-500"></FireOutlined>
              </template>
            </li>
          </RouterLink>
        </ul>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 bg-gray-900 m-8 lg:m-12">
      <div class="gap-8">
        <!-- Search Bar -->
        <div id="action-bar" class="flex flex-col-reverse lg:flex-row justify-between w-full gap-12">
          <div class="flex items-center gap-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="w-8 h-8 hidden lg:block">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
            </svg>
            <div class="relative w-full hi max-w-lg flex">
              <input type="text" class="w-full min-w-96 py-4 pl-12 focus:outline-none rounded-2xl  bg-gray-700">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                class="w-auto h-8 absolute left-0 top-0 m-3">
                <path fill-rule="evenodd"
                  d="M10.51 3.75a6.75 6.75 0 1 0 0 13.5 6.75 6.75 0 0 0 0-13.5ZM2.25 10.5a8.25 8.25 0 1 1 14.59 5.28l4.69 4.69a.75.75 0 1 1-1.06 1.06l-4.69-4.69A8.25 8.25 0 0 1 2.25 10.5Z"
                  clip-rule="evenodd" />
              </svg>
            </div>

          </div>

          <div class="flex justify-around  items-center">
            <div>
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="lg:hidden w-12 h-12">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
              </svg>
            </div>
            <div class="flex w-full justify-end gap-8 items-center">
              <PlusIcon class="h-8 p-1 border-2 rounded-full "></PlusIcon>
              <BellIcon class="h-10"></BellIcon>
              <UserIcon class="hover:bg-gray-600 h-12 p-1 bg-gray-700 rounded-xl"></UserIcon>
            </div>

          </div>
        </div>
        <!-- Breadcrumb -->
        <div id="breadcrumb" class="my-5 mx-2 text-gray-500 font-semibold">Home > Community Forums </div>

        <!-- IntroBox and Staff Online -->
        <div class="flex flex-col xl:flex-row">
          <div class="flex-1">
            <IntroBox class="hidden" />
          </div>
          <StaffOnline class="lg:mx-16 hidden" />
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
