<template>
  <transition name="modal">
    <div v-if="isModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black bg-opacity-50 backdrop-blur-sm">
      <div class="relative p-8 bg-gray-800 rounded-lg w-96 ">

        <slot name="title">
          <h2 class="mb-4 text-xl font-bold ">Modal Title</h2>
        </slot>

        <slot name="content">
          <div class="">
            Default modal content...
          </div>
        </slot>

        <slot name="emojis">
        
          <div v-if="emojis" class="grid my-2 min-w-lg">
            <p class="py-2">Insert Emojis</p>
            <div v-for="(row, rowIndex) in smileyRows" :key="rowIndex" class="flex justify-start gap-2">
              <div v-for="(smiley, index) in smileys[row]" :key="index" @click="insertEmojis(smiley)">
                {{ smiley }}
              </div>
            </div>
          </div>
        </slot>

        <slot name="button">
          <button @click="defaultButtonClick" class="px-4 py-2 mt-4 text-white bg-blue-500 rounded-md">Button</button>
        </slot>

        <button @click="closeModal"
          class="absolute top-0 right-0 mt-2 mr-2 text-gray-400 hover:text-gray-300 focus:outline-none">
          <XCircleIcon class="w-6 h-6"></XCircleIcon>
        </button>
      </div>
    </div>
  </transition>
</template>

<script lang="ts">
import { XCircleIcon } from '@heroicons/vue/24/solid'
import { defineComponent, type PropType } from 'vue';

interface Emoji {
  [key: string]: string[];
}

export default defineComponent({
  components: {
    XCircleIcon
  },
  props: {
    isModalOpen: Boolean,
    emojis: Object as PropType<Emoji>,
  },
  methods: {
    closeModal() {
      this.$emit('close');
    },
    insertEmojis(smiley: string) {
      this.$emit('insert-emoji', smiley);
    },
    defaultButtonClick() {

    }
  },
  computed: {
    smileys(): Emoji {
      return this.emojis || {
        '1': ['😀', '😁', '😂', '😃', '😄', '😅'],
        '2': ['😆', '😇', '😈', '😉', '😊', '😋'],
        '3': ['😌', '😍', '😎', '😏', '😐', '😑'],
        '4': ['😒', '😓', '😔', '😕', '😖', '😗']
      };
    },
    smileyRows(): string[] {
      return Object.keys(this.smileys);
    }
  }
});
</script>

