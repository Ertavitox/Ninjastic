<template>
    <div class="flex flex-col max-w-7xl">
        <div class="max-w-xs bg-gray-800 rounded-lg">
            <div class="p-3 border-t-8 border-blue-500 rounded-t-lg" v-if="loading">Loading...</div>
            <div v-else-if="profile">
                <div class="p-3 border-t-8 rounded-t-lg " :class="profile.status === 1 ? 'border-green-500' : 'border-red-500'">
                <div class="px-2">{{ profile.name }} ({{ profile.id }})</div>
                <div class="px-2 text-gray-400/50">User</div>
                </div>
             
            </div>
            <div v-else class="p-3 border-t-8 border-red-500 rounded-t-lg">No profile found</div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth.store';


interface Profile {
    id: number;
    name: string;
    status: number;
    email: string;
}

export default defineComponent({
    setup() {

        const apiUrl = import.meta.env.VITE_API_URL;
        const auth = useAuthStore();
        const token = computed(() => auth.getToken());
        const router = useRouter();
        const profileId = router.currentRoute.value.params.id;
        const profile = ref<Profile | null>(null);
        const loading = ref(true);

        const fetchProfile = async () => {
            try {
                const response = await fetch(`${apiUrl}/users/${profileId}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token.value}`
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    profile.value = data.result;
                } else {
                    console.error('Failed to fetch profile details');
                }
            } catch (error) {
                console.error('Error fetching profile details:', error);
            } finally {
                loading.value = false;
            }
        };

        onMounted(() => {
            fetchProfile();
        });

        return {
            profile,
            loading
        };
    }
});
</script>