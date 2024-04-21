import { defineStore } from 'pinia';

export interface SearchState {
  searchTerm: string;
}

export const useSearchStore = defineStore({
  id: 'search',
  state: (): SearchState => ({
    searchTerm: ''
  }),
  actions: {
    updateSearchTerm(newTerm: string) {
      this.searchTerm = newTerm;
    }
  }
});
