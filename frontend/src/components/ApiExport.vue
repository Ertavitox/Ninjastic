<script lang="ts">
import { defineProps } from 'vue';

// Importing the API URL from environment variables
export const API_URL = import.meta.env.VITE_API_URL;

console.log(API_URL)
// Define component props
const props = defineProps({
  // Define your props here if any
});

// Define other component logic here
</script>