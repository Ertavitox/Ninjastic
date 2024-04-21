<template>
  <transition name="modal">
    <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black bg-opacity-50 backdrop-blur-sm">
      <div class="relative p-8 bg-gray-800 rounded-lg w-96 ">
        <!-- Use named slot for modal content -->
        <slot name="content"></slot>
        <!-- Close icon positioned in the top-right corner -->
        <button @click="closeModal" class="absolute top-0 right-0 mt-2 mr-2 text-gray-400 hover:text-gray-300 focus:outline-none">
          <XCircleIcon class="w-6 h-6"></XCircleIcon>
        </button>
      </div>
    </div>
  </transition>
</template>

<script lang="ts">
import { XCircleIcon } from '@heroicons/vue/24/solid'
import { defineComponent } from 'vue';
export default defineComponent({
  components: {
    XCircleIcon
  },
  props: {
    isModalOpen: Boolean,
  },
  methods: {
    closeModal() {
      this.$emit('close');
    },
  },
});
</script>

