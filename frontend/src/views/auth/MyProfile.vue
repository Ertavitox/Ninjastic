<template>
    <div
        class="relative flex flex-col w-full gap-8 p-6 bg-gray-800 lg:p-12 lg:items-center lg:flex-row rounded-xl max-w-7xl">

            <div class="flex flex-col gap-6">
                <h2 class="text-2xl font-bold">My Profile</h2>
                <div>
                    <template v-if="!auth.userData?.username">
                        <UserIcon class="w-auto h-12 p-2 bg-gray-500 rounded-full lg:mx-2 "></UserIcon>
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
                            <span class="w-16">Name: </span>
                            <span class="inline-flex items-center gap-2">{{ auth.userData?.name }} <a
                                    class="text-xs cursor-pointer text-secondary/80">Change Name</a></span>
                        </div>
                        <div class="inline-flex gap-4">
                            <span class="w-16">Email: </span>
                            <span class="inline-flex items-center gap-2">{{ auth.userData?.username }} <a
                                    class="text-xs cursor-pointer text-secondary/80">Change Username/Email</a></span>
                        </div>
                        <div class="inline-flex gap-4">
                            <span class="w-16">Password: </span>
                            <span class="inline-flex items-center gap-2 mt-1"> *********** <a
                                    class="-mt-1 text-xs cursor-pointer text-secondary/80">Change Password</a></span>
                        </div>
                        <div class="flex items-center gap-2 px-4 py-2 my-2 text-green-800 border-2 border-green-800 rounded-lg bg-green-300/80">
                            <InformationCircleIcon class="w-auto h-8" />
                            Do you want to
                            change your avatar? Register and change it here.</div>

                    </div>
                </div>


            </div>


    </div>
</template>
<script setup lang="ts">
import { ref } from 'vue';
import { UserIcon } from '@heroicons/vue/24/solid'
import { InformationCircleIcon } from '@heroicons/vue/24/outline';
import GravatarImage from '@/components/GravatarImage.vue'
import { useAuthStore } from '@/stores/auth.store'


const baseURL = import.meta.env.VITE_APP_URL;

const auth = useAuthStore();

</script>
