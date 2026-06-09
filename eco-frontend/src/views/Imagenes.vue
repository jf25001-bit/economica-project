<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const imagenes = ref([])

onMounted(async () => {
    try {
        const response = await axios.get('http://127.0.0.1:8000/api/imagenes')
        imagenes.value = response.data
    } catch (error) {
        console.error(error)
    }
})
</script>

<template>
    <div>
        <h1>Imágenes</h1>

        <div v-for="imagen in imagenes" :key="imagen.id">
            <img
                :src="`http://127.0.0.1:8000/storage/${imagen.ruta}`"
                width="200"
            >
        </div>
    </div>
</template>