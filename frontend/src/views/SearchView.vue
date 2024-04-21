<script setup lang="ts">
import { useSearchStore } from '@/stores/search.store';
import { ref } from 'vue';
import { useRouter } from 'vue-router';

// Define the interface for search results
interface Results {
    id: number;
    snippet: string;
    title: string;
}

// Initialize searchResults as a ref array of Results
const searchResults = ref([] as Results[]);
const router = useRouter();

// Define the handleSearch function to fetch search results
const handleSearch = async () => {
  try {
    const response = await fetch(`/api/search?term=${searchTerm.searchTerm}`);
    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }
    const data = await response.json();
    searchResults.value = data.results;
  } catch (error) {
    console.error('Error fetching search results:', error);
  }
};

// Use the searchTerm from the store
const searchTerm = useSearchStore();
</script>

<template>
  <div class="flex flex-col">
    <h2 class="text-xl">Search for <span class="font-bold">{{ searchTerm.searchTerm }}</span></h2>
    <div class="my-4">
      <ul v-if="searchResults.length">
        <li v-for="result in searchResults" :key="result.id">
          <router-link :to="'/thread/' + result.id">{{ result.title }}</router-link>
          <p>{{ result.snippet }}</p>
        </li>
      </ul>
      <p v-else>No search results found.</p>
    </div>
  </div>
</template>
