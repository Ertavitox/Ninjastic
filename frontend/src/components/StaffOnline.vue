<script setup lang="ts">
import { UsersIcon } from '@heroicons/vue/24/outline'
import { UserIcon } from '@heroicons/vue/24/solid';

const staffList = [
    {
        name: "Marcell Csendes",
        role: "Frontend Developer",
    }
];
</script>

<template>
    <div>
        <div class="hidden lg:flex justify-center items-center bg-gray-900 flex-col">
            <div class="flex items-center gap-3 mt-12">
                <UsersIcon class="w-7 text-gray-500" />
                <span class="font-semibold text-xl">Staff Online</span>
            </div>
            <div id="staff-list" class="gap-4 mt-12">
                <div class="w-full grid grid-cols-3 xl:grid-cols-1  gap-6 lg:gap-6">
                    <div v-for="(staff, index) in staffList" :key="index" :id="`staff-${index+1}`" class="flex items-center gap-4">
                        <UserIcon class="h-16 p-4 rounded-xl bg-gray-600 " />
                        <div id="staffInfo">
                            <div id="staffName">{{ staff.name }}</div>
                            <span class="text-gray-500">{{ staff.role }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col lg:hidden bg-gray-800 p-6 rounded-xl">
            <div class="flex items-center gap-3">
                <UsersIcon class="w-7 text-gray-500" />
                <span class="font-semibold text-xl text-gray-500">Staff Online</span>
            </div>
            <div class="text-lg flex mt-2 text-gray-300">{{ staffList.map(staff => staff.name).join(', ') }}</div>
        </div>
    </div>
</template>
