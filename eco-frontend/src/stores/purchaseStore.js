import { defineStore } from 'pinia'
import { ref } from 'vue'

export const usePurchaseStore = defineStore('purchase', () => {
  const selectedProvider = ref(null)
  const purchaseDetails = ref([])

  return {
    selectedProvider,
    purchaseDetails
  }
})