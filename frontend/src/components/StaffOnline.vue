<script setup lang="ts">
import { UsersIcon } from '@heroicons/vue/24/outline'
import { UserIcon } from '@heroicons/vue/24/solid';
</script>

<template>
    <div class="mr-16 flex justify-center items-center bg-gray-900 flex-col">
        <div class="flex items-center gap-3 ">
            <UsersIcon class="w-7 text-gray-500" />
            <span class="font-semibold text-xl">Staff Online</span>
        </div>
        <div id="staff-list" class="gap-4 mt-12">
            <div class="w-full grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-6 lg:gap-6">
                <div id="staff-1" class="flex items-center gap-4">
                    <UserIcon class="h-16 p-4 rounded-xl bg-gray-600 " />
                    <div id="staffInfo">
                        <div id="staffName">Marcell Csendes</div>
                        <span class="text-gray-500">Frontend Developer</span>
                    </div>
                </div>
                <div id="staff-2" class="flex items-center gap-4">
                    <UserIcon class="h-16 p-4 rounded-xl bg-gray-600 " />
                    <div id="staffInfo">
                        <div id="staffName">Erdős Attila</div>
                        <span class="text-gray-500">Backend Developer</span>
                    </div>
                </div>
                <div id="staff-3" class="flex items-center gap-4">
                    <UserIcon class="h-16 p-4 rounded-xl bg-gray-600 " />
                    <div id="staffInfo">
                        <div id="staffName">Jancsik Balázs</div>
                        <span class="text-gray-500">Backend Developer</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>