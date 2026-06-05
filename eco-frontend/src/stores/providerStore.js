import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useProviderStore = defineStore('provider', () => {
  const providers = ref([])

  return {
    providers
  }
})